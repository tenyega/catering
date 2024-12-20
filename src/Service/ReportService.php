<?php

namespace App\Service;

use App\Repository\PaymentRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReportService
{
    private $em;
    private $pr;
    private $or; 
    public function __construct(EntityManagerInterface $em, PaymentRepository $pr, OrderRepository $or)
    {
        $this->em = $em;
        $this->pr= $pr; 
        $this->or= $or;
    }

    public function generateSalesReport(): array
    {
        // Fetch total revenue
        $totalRevenue = $this->pr->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->getQuery()
            ->getSingleScalarResult();

        // Fetch total orders
        $totalOrders = $this->or->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Fetch average order value
        $avgOrderValue = $totalRevenue / max($totalOrders, 1);

        return [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
        ];
    }

    // Other report generation methods...
}
