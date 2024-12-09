<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository; 
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


    // this lists the orders of the user connected, here i have taken the userid 5  
    #[Route('/user/order', name: 'app_order')]
    public function index(Request $request,OrderRepository $or, UserRepository  $cr, OrderItemRepository $oir): Response
    {  
        $user = $cr->find(5);

        $orderStatus = $request->query->get('orderStatus'); // Get filter value for orderStatus
        $paymentStatus = $request->query->get('paymentStatus'); // Get filter value for paymentStatus
    
        $criteria = ['user' => $user];

if ($orderStatus) {
    $criteria['orderStatus'] = $orderStatus;
}
if ($paymentStatus) {
    $criteria['paymentStatus'] = $paymentStatus;
}
$orders = $or->findBy($criteria);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'user'=>$user,
            'route'=>'app_order'
        ]);
    }
   
     // this lists all the orders  
     #[Route('/orders', name: 'app_orderList')]
     public function orderList(Request $request, OrderRepository $or, UserRepository  $cr, OrderItemRepository $oir): Response
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
