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
                'required' => false,
                'row_attr' => ['class' => 'form-group mb-4'],
                'attr' => [

                    'placeholder' => 'Enter the name',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Name is required.']),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group mb-4'],
                'attr' => [

                    'placeholder' => 'Enter a description',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description is required.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Description cannot exceed 255 characters.',
                    ]),
                ],
            ])
            ->add('price', TextType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group mb-4'],
                'attr' => [

                    'placeholder' => 'Enter the price',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Price is required.']),
                    new Assert\Positive(['message' => 'Price must be positive']),
                    new Assert\Regex([
                        'pattern' => '/^\d+(\.\d+)?$/',
                        'message' => 'Please enter a valid numeric price.',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group mb-4'],

                'choices' => [
                    'Starter' => 'STARTER',
                    'Beverage' => 'BEVERAGE',
                    'Snacks' => 'SNACKS',
                    'Main Course' => 'MAIN COURSE',
                    'Dessert' => 'DESSERT',
                ],
                'placeholder' => 'Select a category',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Category is required.']),
                ],
            ])
            ->add('isAvailable', CheckboxType::class, [

                'attr' => ['class' => 'mr-2 rounded'],
                'required' => false,
                'mapped' => false,
            ])
            ->add('img', FileType::class, [
                'label' => 'Image (JPG/PNG file)',

                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
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
            'data_class' => MenuItem::class,
        ]);
    }
}
