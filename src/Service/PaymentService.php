<?php

namespace App\Service;

use App\Entity\Payment;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
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
        dump(
            'hi'
        );
        $this->addPayment($id);
        return $checkoutSession;
    }
    //traitement du role de utilisateurs en fonction du paiement. 
    public function addPayment(int $id): ?Payment
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



        return $payment;
    }
}
