<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
class EmployeeController extends AbstractController
{

    private $entityManager;
    private $hasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }


    #[Route('/', name: 'app_employee')]
    public function index(EmployeeRepository $er): Response
    {

        $qb = $er->createQueryBuilder('e');
        $qb->where('e.roles LIKE :role')
            ->setParameter('role', '%ROLE_EMPLOYEE%')
            ->orderBy('e.email', 'ASC');

        $employees = $qb->getQuery()->getResult();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }


    #[Route('/edit/{id}', name: 'employee_edit')]
    public function editEmployee(int $id, EmployeeRepository $er, Request $request): Response
    {

        $employee = $er->findOneBy(['id' => $id]);
        if (!$employee) {
            throw $this->createNotFoundException('employees not found');
        }

        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee->setPassword($this->hasher->hashPassword($employee, $form->get('password')->getData()));


            $this->entityManager->flush();
            $this->addFlash("success", "Your employee has been updated Successfully");
            return $this->redirectToRoute('app_employee');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'employee_delete')]

    public function deleteEmployee(Employee $employee): Response
    {

        $this->entityManager->remove($employee);
        $this->entityManager->flush();
        $this->addFlash("success", "Your Employee has been deleted Successfully");
        return $this->redirectToRoute('app_employee');
    }

    #[Route('/add', name: 'employee_add')]

    public function addEmployee(Request $request, EmployeeRepository $er): Response
    {


        $form = $this->createForm(EmployeeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $emailExist = $er->findOneBy(['email' => $email]);
            if ($emailExist) {
                $this->addFlash("error", "An Employee already exist with this email id");
                return $this->redirectToRoute('app_employee');
            } else {
                $employee = new Employee();
                $employee->setEmail($form->get('email')->getData())
                    ->setPassword($this->hasher->hashPassword($employee, $form->get('password')->getData()))
                    ->setRoles(['ROLE_EMPLOYEE']);
                $this->entityManager->persist($employee);
                $this->entityManager->flush();
                $this->addFlash("success", "A new Employee has been added Successfully");
                return $this->redirectToRoute('app_employee');
            }
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
