<?php

namespace App\Form;

use App\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Name is required.']),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description is required.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Description cannot exceed 255 characters.',
                    ]),
                ],
            ])
            ->add('price', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Price is required.']),
                    new Assert\Positive(['message' => 'Price must be a positive number.']),
                ],
            ])
            ->add('category', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Category is required.']),
                ],
            ])
            ->add('isAvailable', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Type(['type' => 'bool', 'message' => 'Invalid value for availability.']),
                ],
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
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG image.',
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
