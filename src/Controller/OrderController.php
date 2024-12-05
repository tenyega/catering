<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use Symfony\Component\HttpFoundation\Request;



class OrderController extends AbstractController
{
    private $entityManager;
   
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }


    // this lists the orders of the customer connected, here i have taken the customerid 16  
    #[Route('/customer/order', name: 'app_order')]
    public function index(Request $request,OrderRepository $or, CustomerRepository  $cr, OrderItemRepository $oir): Response
    {  
        $customer = $cr->find(16);

        $orderStatus = $request->query->get('orderStatus'); // Get filter value for orderStatus
        $paymentStatus = $request->query->get('paymentStatus'); // Get filter value for paymentStatus
    
        $criteria = ['customer' => $customer];

if ($orderStatus) {
    $criteria['orderStatus'] = $orderStatus;
}
if ($paymentStatus) {
    $criteria['paymentStatus'] = $paymentStatus;
}
$orders = $or->findBy($criteria);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'customer'=>$customer,
            'route'=>'app_order'
        ]);
    }
   
     // this lists all the orders  
     #[Route('/orders', name: 'app_orderList')]
     public function orderList(Request $request, OrderRepository $or, CustomerRepository  $cr, OrderItemRepository $oir): Response
     {  
        $orderStatus = $request->query->get('orderStatus'); // Get filter value for orderStatus
        $paymentStatus = $request->query->get('paymentStatus'); // Get filter value for paymentStatus
    
        // Build the query criteria based on the filters
        $criteria = [];
        if ($orderStatus) {
            $criteria['orderStatus'] = $orderStatus;
        }
        if ($paymentStatus) {
            $criteria['paymentStatus'] = $paymentStatus;
        }
    
        // Fetch filtered orders
        $orders = $or->findBy($criteria);
        
 
         return $this->render('order/index.html.twig', [
             'orders' => $orders,
            'route' => 'app_orderList'

         ]);
     }
}
