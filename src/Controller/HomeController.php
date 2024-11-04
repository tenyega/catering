<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(OrderRepository $or): Response
    {
        $orders = $or->findBy([
            'paymentStatus' => 'PAID',
            'orderStatus' => 'PROCESSED'
        ]);
        return $this->render('home/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
