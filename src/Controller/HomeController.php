<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MenuItemRepository $mir, PaginatorInterface $paginator,  SessionInterface $sessionInterface, Request $request): Response
    {
        $title = "Catering Cash Register 🍕";
        // $session = $request->getSession(); // Get the session
        // $session->clear();
        $menuItems = $mir->findBy([
            "isAvailable" => true
        ]);

        $pagination = $paginator->paginate(
            $menuItems, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            9 /* limit per page */
        );

        // $menuItems = $mir->findAll();
        return $this->render('home/index.html.twig', [
            'menuItems' => $menuItems,
            'title' => $title,
            'pagination' => $pagination
        ]);
    }

    #[Route('/search/{filter}', name: 'app_filter')]
    public function starter(string $filter, MenuItemRepository $mir, PaginatorInterface $paginator, Request $request)
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

        $pagination = $paginator->paginate(
            $menuItems, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            9 /* limit per page */
        );
        return $this->render('home/index.html.twig', [
            'menuItems' => $menuItems,
            'title' => $title,
            'pagination' => $pagination
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

    #[Route('/404', name: 'app_404')]
    public function pageNotFound(): Response
    {
        return $this->render('home/404.html.twig', []);
    }

    #[Route('/403', name: 'access_denied')]
    public function access_denied(): Response
    {
        return $this->render('home/access_denied.html.twig', []);
    }
}
