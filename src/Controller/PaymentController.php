<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\MenuItemRepository;
use App\Repository\UserRepository;
use Stripe\Webhook;
use App\Security\EmailVerifier;
use App\Service\HourCalculator;
use App\Service\PaymentService;
use Symfony\Component\Mime\Address;
use App\Repository\ReservationRepository;
use App\Service\EmailNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PaymentController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}
    // Route lorsque le paiement est réussi
    #[Route('/payment-success/{id}', name: 'app_payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request, PaymentService $ps, int $id, SessionInterface $sessionInterface): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {

            // Clearing the session cart only when the payment is successful.
            $sessionInterface->set('cart', []);

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $this->getUser(),
                (new TemplatedEmail())
                    ->from(new Address('mdolma@ymail.com', 'Catering Service'))
                    ->to((string) $this->getUser()->getUserIdentifier())
                    ->subject('Thank you for your Payment')
                    ->htmlTemplate('payment/paymentConfirmationMail.html.twig')
            );

            return $this->render('payment/payment-success.html.twig');
        } else {
            $this->addFlash('error', "You can't order  without a payment");
            return $this->redirectToRoute('cart_pay');
        }

        return $this->render('payment/payment-success.html.twig');
    }

    // Route lorsque le paiement a échoué
    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(Request $request): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {
            return $this->render('payment/payment-cancel.html.twig');
        } else {
            $this->addFlash('error', "You can't order  without a payment");
            return $this->redirectToRoute('cart_pay');
        }
    }


    /**
     * Redirection vers le paiement Stripe
     * Ici on utilise la classe RedirectResponse de HttpFoundation
     * Cela nous donne accès à la méthos redirect qui génère la requête
     * à partir de la session initié avec PaymentService->askCheckout()
     *
    #[Route('/payment/checkout', name: 'app_payment_checkout', methods: ['GET'])]
    public function checkout(PaymentService $ps, SessionInterface $sessionInterface): RedirectResponse
    {
        dd($sessionInterface->get('cart'));
        return $this->redirect($ps->askCheckout($id)->url);
    }
     */
    /**
     * Check du statut de paiement
     */
    #[Route('/payment-webhook', name: 'app_stripe_webhook', methods: ['GET', 'POST'])]
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
            dd($event);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }
    }

    #[Route('/c/pay', name: 'cart_pay')]
    public function cart_pay(SessionInterface $sessionInterface, MenuItemRepository $mir, UserRepository $cr, EntityManagerInterface $entityManagerInterface, PaymentService $ps, Request $request)
    {
       $specialRequest = $request->request->get('specialRequest');
       

        $total = 0.0; // Start as a float
        $totalQuantity = 0;

        foreach ($sessionInterface->get('cart', []) as $id => $qty) {
            $menuItem = $mir->find($id);
            if (!$menuItem) {
                continue; // Skip if menu item not found
            }
            $detailedCart[] = [
                'menuItem' => $menuItem,
                'qty' => $qty,
            ];
            $totalQuantity += $qty;
            $total += ($menuItem->getPrice() * $qty);
        }

        $order = new Order;
        $order->setTotalAmount((float) $total)
            ->setPaymentStatus('PENDING')
            ->setOrderStatus('PROCCESSING')
            ->setSpecialRequest($specialRequest)
            ->setUser($cr->findOneBy(['id' => $this->getUser()->getId()]));


        // Persist and flush to save in the database
        $entityManagerInterface->persist($order);
        $entityManagerInterface->flush();

        // Ensure the ID and total amount are saved correctly
        $cart = $sessionInterface->get('cart', []);

        foreach ($cart as $menuItemID => $qty) {
            $menuItm = $mir->find($menuItemID);
            $orderItem = new OrderItem;
            $orderItem->setQuantity($qty)
                ->setMenuItem($menuItm)
                ->setOrders($order)
                ->setItemPrice($menuItm->getPrice() * $qty);
            $entityManagerInterface->persist($orderItem);
        }
        $entityManagerInterface->flush();
        //$sessionInterface->set('cart', []); This i have moved it to the payment success method 
        $orderID = $order->getId();

        // this was a cruial step using js here to relocate the url to the check out url generated by the stripe. otherwise i used to get url cut to half using the different methods like 
        //$this->redirect(), ResponseRedirect() etc. 
        /*Possible Causes of URL Truncation:


PHP configuration settings
Web server (Apache/Nginx) URL length restrictions
Framework-specific routing limitations
Encoding issues
*/

        $fullUrl = $ps->askCheckout($orderID)->url;
        echo "<script>window.location.href = '" . htmlspecialchars($fullUrl) . "';</script>";

    
    }


    #[Route('/pay/cash', name: 'pay_cash', methods: ['GET', 'POST'])]
    public function payCash(Request $request): Response
    {
        return $this->render('payment/cash_payment.html.twig');
    }



    #[Route('/pay/tr', name: 'tr_payment', methods: ['GET', 'POST'])]
    public function trPayment(Request $request): Response
    {
        return $this->render('payment/tr_payment.html.twig');
    }
}
