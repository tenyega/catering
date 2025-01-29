<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RGPDController extends AbstractController
{
    #[Route('/rgpd/legal', name: 'app_legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('rgpd/legalNotice.html.twig', [
            'controller_name' => 'RGPDController',
        ]);
    }

    #[Route('/rgpd/policy', name: 'app_policy')]
    public function policy(): Response
    {
        return $this->render('rgpd/policy.html.twig', [
            'controller_name' => 'RGPDController',
        ]);
    }
}
