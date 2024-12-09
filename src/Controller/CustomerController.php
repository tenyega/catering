<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class CustomerController extends AbstractController
{

    private $entityManager;
    private $hasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }


    #[Route('/customer', name: 'app_customer')]
    public function index(CustomerRepository $cr): Response
    {

        $customers = $cr->findBy([], ['firstName' => 'ASC']);
        return $this->render('customer/index.html.twig', [
            'customers' => $customers,
        ]);
    }


    #[Route('/customer/edit/{id}', name: 'customer_edit')]
    public function editCustomer(int $id, CustomerRepository $cr, Request $request): Response
    {

        $customer = $cr->findOneBy(['id' => $id]);
        if (!$customer) {
            throw $this->createNotFoundException('customer not found');
        }

        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setPassword($this->hasher->hashPassword($customer, $form->get('password')->getData()));


            $this->entityManager->flush();
            $this->addFlash("success", "Your Customer has been updated Successfully");
            return $this->redirectToRoute('app_customer');
        }

        return $this->render('customer/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/customer/delete/{id}', name: 'customer_delete')]

    public function deleteCustomer(Customer $customer): Response
    {

        $this->entityManager->remove($customer);
        $this->entityManager->flush();
        $this->addFlash("success", "Your Customer has been deleted Successfully");
        return $this->redirectToRoute('app_customer');
    }

    #[Route('/customer/add', name: 'customer_add')]

    public function addCustomer(Request $request, CustomerRepository $cr): Response
    {

        $errorMsg = "";

        $form = $this->createForm(CustomerType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = new Customer();
            $customer = $form->getdata();

            $email = $form->get('email')->getData();
            $emailExist = $cr->findOneBy(['email' => $email]);
            if ($emailExist) {
                $this->addFlash("error", "Email exist already");
            } else {
                $customer->setEmail($form->get('email')->getData())
                    ->setPassword($this->hasher->hashPassword($customer, $form->get('password')->getData()))
                    ->setFirstName($form->get('firstName')->getData())
                    ->setLastName($form->get('lastName')->getData())
                    ->setAddress($form->get('address')->getData())
                    ->setPhone($form->get('phone')->getData())
                    ->setRoles(['ROLE_USER']);
                $this->entityManager->persist($customer);
                $this->entityManager->flush();
                $this->addFlash("success", "A new Customer has been added Successfully");
                return $this->redirectToRoute('app_customer');
            }
        }

        return $this->render('customer/edit.html.twig', [
            'form' => $form->createView(),
            'errorMsg' => $errorMsg
        ]);
    }
}
