<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Entity\User;
use App\Form\ChangePasswordType;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserController extends AbstractController
{
    private $entityManager;
    private $hasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }


    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $ur): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('home/access_denied.html.twig');
        }

        $users = $ur->findBy([], ['firstName' => 'ASC']);
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function editUser(int $id, UserRepository $ur, Request $request): Response
    {

      
        $user = $ur->findOneBy(['id' => $id]);
        if (!$user) {
            throw $this->createNotFoundException('user not found');
        }

        if($user != $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
            return $this->render('home/access_denied.html.twig');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));


            $this->entityManager->flush();
            $this->addFlash("success", "Your user has been updated Successfully");
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]

    public function deleteUser(User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') ) {
            return $this->render('home/access_denied.html.twig');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash("success", "Your user has been deleted Successfully");
        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/add', name: 'user_add')]

    public function addUser(Request $request, UserRepository $ur): Response
    {

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            return $this->render('home/access_denied.html.twig');
        }
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $emailExist = $ur->findOneBy(['email' => $email]);
            if ($emailExist) {
                $this->addFlash("error", "This email is already in use, please use another email address");
                return $this->redirectToRoute('app_user');
            } else {
                $user = new user();
                $user->setEmail($form->get('email')->getData())
                    ->setPassword($this->hasher->hashPassword($user, $form->get('firstName')->getData()))
                    ->setFirstName($form->get('firstName')->getData())
                    ->setLastName($form->get('lastName')->getData())
                    ->setAddress($form->get('address')->getData())
                    ->setPhone($form->get('phone')->getData())
                    ->setRoles(['ROLE_USER']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash("success", "A new user has been added Successfully");
                return $this->redirectToRoute('app_user');
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/user/view/{id}', name: 'user_view')]

    public function viewUser(User $user): Response
    {
        if($user != $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
            return $this->render('home/access_denied.html.twig');
        }

        return $this->render('user/view.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/change/pwd', name: 'user_pwd_change')]
    public function changePwd(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);

        if($user != $this->getUser()){
            return $this->render('home/access_denied.html.twig');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Verify the current password
            if (!$this->hasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash("error", "Actual password is not correct");
                return $this->redirectToRoute('app_home');
            }

            // Hash and update the new password
            $hashedNewPassword = $this->hasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedNewPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash("success", "Your password has been changed successfully");
            return $this->redirectToRoute('app_home'); // Redirect to a relevant route
        }



        return $this->render('user/user_pwd_change.html.twig', [
            'form' => $form
        ]);
    }
}
