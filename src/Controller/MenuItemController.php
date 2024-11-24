<?php

namespace App\Controller;


use App\Entity\MenuItem;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class MenuItemController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/menu/item', name: 'app_menu_item')]
    public function index(MenuItemRepository $mir): Response
    {
        $menuItems = $mir->findBy([], ['name' => 'ASC']);
        return $this->render('menu_item/index.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }


    #[Route('/menu/item/edit/{id}', name: 'menu_item_edit')]
    public function editMenuItem(int $id, MenuItemRepository $mir, Request $request): Response
    {

        $menuItem = $mir->findOneBy(['id' => $id]);
        if (!$menuItem) {
            throw $this->createNotFoundException('Menu item not found');
        }

        $form = $this->createForm(MenuItemType::class, $menuItem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash("success", "Your Menu Item has been updated Successfully");
            return $this->redirectToRoute('app_menu_item');
        }

        return $this->render('menu_item/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/menu/item/delete/{id}', name: 'menu_item_delete')]

    public function deleteMenuItem(MenuItem $menuItem): Response
    {

        $this->entityManager->remove($menuItem);
        $this->entityManager->flush();
        $this->addFlash("success", "Your Menu Item has been deleted Successfully");
        return $this->redirectToRoute('app_menu_item');
    }

    #[Route('/menu/item/add', name: 'menu_item_add')]

    public function addMenuItem(Request $request): Response
    {


        $form = $this->createForm(MenuItemType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuItem = new MenuItem();
            $isAvailable = $form->get('isAvailable')->getData();
            $menuItem->setName($form->get('name')->getData())
                ->setCategory($form->get('category')->getData())
                ->setDescription($form->get('description')->getData())
                ->setImg($form->get('img')->getData())
                ->setPrice($form->get('price')->getData())
                ->setAvailable($isAvailable);
            $this->entityManager->persist($menuItem);
            $this->entityManager->flush();
            $this->addFlash("success", "A new Menu Item has been added Successfully");
            return $this->redirectToRoute('app_menu_item');
        }

        return $this->render('menu_item/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
