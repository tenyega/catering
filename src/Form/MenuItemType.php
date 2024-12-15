<?php

namespace App\Form;

use App\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Name is required.']),
                ],
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description is required.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Description cannot exceed 255 characters.',
                    ]),
                ],
            ])
            ->add('price', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Price is required.']),
                    new Assert\Positive(['message' => ' Price must be positive'])
                ],
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Starter' => 'STARTER',
                    'Beverage' => 'BEVERAGE',
                    'Snacks' => 'SNACKS',
                    'Main Course' => 'MAIN COURSE',
                    'Dessert' => 'DESSERT',
                ],
                'placeholder' => 'Select a category', // Optional: Placeholder for dropdown
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Category is required.']),
                ],
            ])
            ->add('isAvailable', CheckboxType::class, [
                'mapped' => false, // Not bound to the entity
                'required' => false,
            ])
            ->add('img', FileType::class, [
                'label' => 'Image (JPG/PNG file)',
                'mapped' => false, // Not bound to the entity
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M', // Optional: Set file size limit
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG file.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class, // Replace with your entity class
            'attr' => [
                'novalidate' => 'novalidate', // Disable HTML5 validation
            ],
        ]);
    }
}
