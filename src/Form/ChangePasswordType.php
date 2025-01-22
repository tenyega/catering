<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Security\Core\Security;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Add custom validation logic here if needed
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Current Password',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter your current password']),
                    new Callback(function ($value, ExecutionContextInterface $context) use ($options) {

                        $user = $options['data']['user']; // Get the user passed as an option
                        if (!$user || !password_verify($value, $user->getPassword())) {
                            $context->buildViolation('The current password is incorrect.')
                                ->addViolation();
                        }
                    }),
                ],
                'mapped' => false, // Current password is not saved in the database
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'New Password',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter a new password']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Your password must be at least {{ limit }} characters long',
                    ]),
                ],
                'mapped' => false, // Password update logic will handle this
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirm New Password',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please confirm your new password']),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        // Access form data
                        $form = $context->getRoot(); // Get the root form
                        $newPassword = $form->get('newPassword')->getData();

                        if ($newPassword !== $value) {
                            $context->buildViolation('Passwords do not match.')
                                ->addViolation();
                        }
                    }),
                ],
                'mapped' => false, // Not saved directly in the database
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
