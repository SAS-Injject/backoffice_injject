<?php

namespace App\Form;

use App\Entity\Customers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Client'
              ]
            )
            ->add('logo', FileType::class, [
                'label' => 'Glissez le Logo du client ici',
                'required' => false,
                'mapped' => false,
                'attr' => [
                  'accept' => 'image/png, image/jpeg, image/webp',
                  'data-drop' => 'true',
                ]
              ]
            )
            ->add('logo_alt', TextType::class, [
              'label' => 'Nom Alternatif Logo',
              'required' => false,
              'empty_data' => ''
            ])
            ->add('logo_legend', TextType::class, [
              'label' => 'Légende Logo',
              'required' => false,
              'empty_data' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customers::class,
        ]);
    }
}
