<?php

namespace App\Controller;

use App\Repository\MenuItemRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MenuItemRepository $mir, SessionInterface $sessionInterface): Response
    {
        $title = "Catering Cash Register 🍕";
        $menuItems = $mir->findBy([
            "isAvailable" => true
        ]);

        // $menuItems = $mir->findAll();
        return $this->render('home/index.html.twig', [
            'menuItems' => $menuItems,
            'title' => $title
        ]);
    }

    #[Route('/{filter}', name: 'app_filter')]
    public function starter(string $filter, MenuItemRepository $mir)
    {

        switch ($filter) {
            case 'STARTER':
                $title = "Starters 🥗";
                break;
            case "MAIN COURSE":
                $title = "Main Courses 🍝";
                break;

            case "DESSERT":
                $title = "Desserts 🍩";
                break;

            case "BEVERAGE":
                $title = "Beverages ☕";
                break;
            case "SNACKS":
                $title = "Snacks 🍿";
                break;
            default:
                $title = "Catering Cash Register 🍕";
                break;
        }
        $menuItems = $mir->findBy([
            'category' => $filter
        ]);
        return $this->render('home/index.html.twig', [
            'menuItems' => $menuItems,
            'title' => $title
        ]);
    }

    /*    #[Route('/m/main_course', name: 'app_maincourse')]
    public function maincourse(MenuItemRepository $mir)
    {
        $mainCourses = $mir->findBy([
            'category' => 'MAIN COURSE'
        ]);
        return $this->render('home/maincourse.html.twig', [
            'maincourses' => $mainCourses,
        ]);
    }

    #[Route('/m/dessert', name: 'dessert')]
    public function dessert(MenuItemRepository $mir)
    {
        $desserts = $mir->findBy([
            'category' => 'DESSERT'
        ]);
        return $this->render('home/dessert.html.twig', [
            'desserts' => $desserts,
        ]);
    }

    #[Route('/m/snacks', name: 'snacks')]
    public function snacks(MenuItemRepository $mir)
    {
        $snacks = $mir->findBy([
            'category' => 'SNACKS'
        ]);
        return $this->render('home/snacks.html.twig', [
            'snacks' => $snacks,
        ]);
    }


    #[Route('/m/beverage', name: 'beverage')]
    public function beverages(MenuItemRepository $mir)
    {
        $beverages = $mir->findBy([
            'category' => 'BEVERAGE'
        ]);
        return $this->render('home/beverage.html.twig', [
            'beverages' => $beverages,
        ]);
    } */
}
