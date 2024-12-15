<?php

namespace App\Service;

use App\Entity\Payment;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
use ContainerYZx6bG7\get_Debug_Security_Voter_Security_Access_AuthenticatedVoterService;
use Stripe\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaymentService
{
    // private string $apiKey = $this->parametmer->get('STRIPE_API_SK');

    private $domain;
    private $apiKey;
    private $em;
    private $or;

    public function __construct(protected OrderRepository $orderRepository, protected ParameterBagInterface $parameter, private Security $security, private EntityManagerInterface $entityManagerInterface)
    {
        $this->parameter = $parameter;
        $this->or = $orderRepository;
        // $this->stripe = $stripe; //Creating object of Stripe Class
        $this->apiKey = $this->parameter->get('STRIPE_API_SK');
        $this->domain = 'https://127.0.0.1:8000';
        $this->em = $entityManagerInterface;
    }


    //generate une demande de paiement vers stripe. 
    /**
     * askCheckout()
     * Méthode permettant de créer une session de paiement Stripe
     * @return Stripe\Checkout\Session
     */
    public function askCheckout(int $id): ?Session
    {

        $order = $this->or->find(['id' => $id]);
        $price = $order->getTotalAmount();



        $amountInCents = (int) round($price * 100);
        $order->setPaymentStatus('PAID');
        $order->setOrderStatus('PROCESSING');
        // $this->em->persist($order);
        // $this->em->flush();

        Stripe::setApiKey($this->apiKey); // Établissement de la connexion (requête API)        

        try {

            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'tax_behavior' => 'exclusive',
                        'unit_amount' => $amountInCents, // Stripe utilise des centimes
                        'product_data' => [ // Les informations du produit sont personnalisables
                            'name' => $order->getUser()->getFirstName(),
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->domain . '/payment-success/' . $id,
                'cancel_url' => $this->domain . '/payment-cancel',
                'automatic_tax' => [
                    'enabled' => true,
                ],
            ]);

            $this->addPayment($id);
            return $checkoutSession;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log the specific Stripe error
            // Consider using a proper logging mechanism
            dump($e->getMessage());
            throw $e;
        }
    }
    //traitement du role de utilisateurs en fonction du paiement. 
    public function addPayment(int $id): void
    {

        $order = $this->or->find(['id' => $id]);
        // Adding new payment;
        $payment = new Payment();
        $payment->setAmount($order->getTotalAmount())
            ->setPaymentDate(new \DateTimeImmutable())
            ->setOrderId($order)
            ->setPaymentMethod('CARD')
        ;
        $this->em->persist($payment);
        $this->em->flush();
    }
}
