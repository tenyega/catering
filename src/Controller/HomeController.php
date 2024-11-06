<?php

namespace App\Controller;

use App\Repository\MenuItemRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MenuItemRepository $mir): Response
    {
        $menuItems = $mir->findBy([
            "isAvailable" => true
        ]);

        // $menuItems = $mir->findAll();
        return $this->render('home/index.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }

    #[Route('/starter', name: 'starter')]
    public function starter(MenuItemRepository $mir)
    {
        $starters = $mir->findBy([
            'category' => 'STARTER'
        ]);
        return $this->render('home/starter.html.twig', [
            'starters' => $starters,
        ]);
    }

    #[Route('/main_course', name: 'maincourse')]
    public function maincourse(MenuItemRepository $mir)
    {
        $mainCourses = $mir->findBy([
            'category' => 'MAIN COURSE'
        ]);
        return $this->render('home/maincourse.html.twig', [
            'maincourses' => $mainCourses,
        ]);
    }

    #[Route('/dessert', name: 'dessert')]
    public function dessert(MenuItemRepository $mir)
    {
        $desserts = $mir->findBy([
            'category' => 'DESSERT'
        ]);
        return $this->render('home/dessert.html.twig', [
            'desserts' => $desserts,
        ]);
    }

    #[Route('/snacks', name: 'snacks')]
    public function snacks(MenuItemRepository $mir)
    {
        $snacks = $mir->findBy([
            'category' => 'SNACKS'
        ]);
        return $this->render('home/snacks.html.twig', [
            'snacks' => $snacks,
        ]);
    }


    #[Route('/beverage', name: 'beverage')]
    public function beverages(MenuItemRepository $mir)
    {
        $beverages = $mir->findBy([
            'category' => 'BEVERAGE'
        ]);
        return $this->render('home/beverage.html.twig', [
            'beverages' => $beverages,
        ]);
    }
}
