<?php

namespace App\Controller;


use App\Entity\MenuItem;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class MenuItemController extends AbstractController
{
    /**
     * Here the employee is given access only to view and edit the menu items 
     * and Admin has the extra right of add; delete 
     */
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/menu/item', name: 'app_menu_item')]
    public function index(MenuItemRepository $mir): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            return $this->render('home/access_denied.html.twig');
        }
        $menuItems = $mir->findBy([], ['name' => 'ASC']);
        return $this->render('menu_item/index.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }


    #[Route('/menu/item/edit/{id}', name: 'menu_item_edit')]
    public function editMenuItem(int $id, MenuItemRepository $mir, Request $request): Response
    {

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            return $this->render('home/access_denied.html.twig');
        }
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

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('home/access_denied.html.twig');
        }
        $this->entityManager->remove($menuItem);
        $this->entityManager->flush();
        $this->addFlash("success", "Your Menu Item has been deleted Successfully");
        return $this->redirectToRoute('app_menu_item');
    }

    #[Route('/menu/item/add', name: 'menu_item_add')]

    public function addMenuItem(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('home/access_denied.html.twig');
        }

        $form = $this->createForm(MenuItemType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $menuItem = new MenuItem();


                $uploadedFile = $form->get('img')->getData();

                if ($uploadedFile) {
                    // Generate a custom name for the file
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $customFilename = $form->get('name')->getData() . '.' . $uploadedFile->guessExtension();

                    // Define the path to the public folder
                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/img';
                    //dd($publicDirectory);
                    // Move the file to the public/uploads directory
                    try {
                        $uploadedFile->move($publicDirectory, $customFilename);
                    } catch (FileException $e) {
                        // Handle exception if something happens during file upload
                        throw new \Exception('Failed to upload the file: ' . $e->getMessage());
                    }

                    // (Optional) Save the custom filename to the database if needed
                    // $menuItem = $form->getData();
                    $menuItem->setImg($customFilename); // Assuming 'setImage' exists in your entity
                }




                $isAvailable = $form->get('isAvailable')->getData();

                $menuItem->setName($form->get('name')->getData())
                    ->setCategory($form->get('category')->getData())
                    ->setDescription($form->get('description')->getData())
                    ->setPrice($form->get('price')->getData())
                    ->setAvailable($isAvailable);


                // dd($menuItem);
                $this->entityManager->persist($menuItem);
                $this->entityManager->flush();
                $this->addFlash("success", "A new Menu Item has been added Successfully");
                return $this->redirectToRoute('app_menu_item');
            } else {
                // dd('inside else ');
                // Extract errors and add them as flash messages
                $errors = $this->getFormErrors($form);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
            }
        }

        return $this->render('menu_item/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Helper method to extract form errors.
     */
    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }
}
