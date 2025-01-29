<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')]
class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    // this lists the orders of the user connected, here i have taken the userid 5  
    #[Route('/user/order', name: 'app_order')]
    public function index(Request $request, OrderRepository $or, UserRepository  $ur, PaginatorInterface $paginator): Response
    {
        $user = $ur->find($this->getUser()->getId());

        $orderStatus = $request->query->get('orderStatus'); // Get filter value for orderStatus
        $paymentStatus = $request->query->get('paymentStatus'); // Get filter value for paymentStatus

        $criteria = ['user' => $user];

        if ($orderStatus) {
            $criteria['orderStatus'] = $orderStatus;
        }
        if ($paymentStatus) {
            $criteria['paymentStatus'] = $paymentStatus;
        }

        $orders = $or->findBy($criteria, ['orderDate' => 'DESC']);

        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit per page */
        );
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'user' => $user,
            'route' => 'app_order',
            'pagination' => $pagination
        ]);
    }

    // To trace the most recent order. 
    #[Route('/user/recent/order', name: 'order_recent')]
    public function recentOrder(Request $request, OrderRepository $or, UserRepository  $ur): Response
    {
        $user = $this->getUser();
        $recentOrder = $or->findOneBy(
            ['user' => $user, 'paymentStatus' => 'PAID'],
            ['orderDate' => 'DESC']
        );
        if ($recentOrder) {
            $statusID = $recentOrder->getOrderStatus();

            switch ($statusID) {
                case 'RECEIVED':
                    $statusID = 1;
                    break;
                case 'PROCESSING':
                    $statusID = 2;
                    break;
                case 'DELIVERY':
                    $statusID = 3;
                    break;
                case 'DELIVERED':
                    $statusID = 4;
                    break;
                case 'CANCELLED':
                    $statusID = 5;
                    break;
            }
        } else {
            $statusID = 0;
        }


        return $this->render('order/recent.html.twig', [
            'order' => $recentOrder,
            'statusID' => $statusID
        ]);
    }



    // this lists all the orders  
    #[Route('/orders', name: 'app_orderList')]
    public function orderList(Request $request, OrderRepository $or, PaginatorInterface $paginator, UserRepository  $cr, OrderItemRepository $oir): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            return $this->render('home/access_denied.html.twig');
        }
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
        $orders = $or->findBy($criteria, ['orderDate' => 'DESC']);

        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit per page */
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'route' => 'app_orderList',
            'pagination' => $pagination

        ]);
    }



    // To cancel the order from the user side 
    #[Route('/user/order/cancel', name: 'order_cancel')]
    public function cancelOrder(Request $request, OrderRepository $or, UserRepository  $ur, PaymentRepository $pr): Response
    {
        $user = $this->getUser();
        $recentOrder = $or->findOneBy(
            ['user' => $user, 'paymentStatus' => 'PAID'],
            ['orderDate' => 'DESC']
        );

        $recentOrder->setPaymentStatus('REFUND IN PROCESS');

        $this->entityManager->flush();
        $this->addFlash('success', "Your order is cancelled sucessfully");
        return $this->render('order/cancel.html.twig', [
            'order' => $recentOrder,
        ]);
    }

    #[Route('/user/save/SR', name: 'saveSR')]
    public function specialRequest(Request $request, OrderRepository $or, UserRepository  $ur, PaymentRepository $pr): Response
    {
        $data = json_decode($request->getContent(), true);
        $specialRequest = $data['special_request'] ?? null;
        $user = $this->getUser();
        $recentOrder = $or->findOneBy(
            ['user' => $user, 'paymentStatus' => 'PAID'],
            ['orderDate' => 'DESC']
        );
        $recentOrder->setSpecialRequest($specialRequest);

        $this->entityManager->flush();
    }





    // this lists all the orders  
    #[Route('/order/status/{id}', name: 'order_status')]
    public function orderStatus(Request $request, OrderRepository $or, Order $order, SessionInterface $session): Response
    {

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            return $this->render('home/access_denied.html.twig');
        }


        // Validate the CSRF token
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('order_status' . $order->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // Get the new status from the form data
        $newStatus = $request->request->get('orderStatus');
        if (!$newStatus) {
            $this->addFlash('error', "Invalid order status");
            return $this->redirectToRoute('order_list'); // Redirect back to the order list
        }
        $this->addFlash('success', "The order status has been updated successfully");

        // Update the order status
        $order->setOrderStatus($newStatus);
        $this->entityManager->flush();
        $orders = $or->findAll();
        $this->addFlash('success', "The order status has been updated successfully");

        // Redirect to the orders list
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'route' => 'app_orderList'

        ]);
    }
}
