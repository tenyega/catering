<?php

namespace App\Controller;

use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AdminController extends AbstractController
{

    private $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }


    #[Route('/admin', name: 'admin_report')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('home/access_denied.html.twig');
        }
        $salesReport = $this->reportService->generateSalesReport();

        return $this->render('admin/index.html.twig', [
            'salesReport' => $salesReport,
        ]);
    }
}
