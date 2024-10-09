<?php

namespace App\Form;

use App\Entity\Customers;
use App\Entity\Realization;
use App\Entity\RealizationCategories;
use App\Repository\CustomersRepository;
use App\Repository\RealizationCategoriesRepository;
use App\Traits\ArticlesFormParametersTraits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RealizationFormType extends AbstractType
{

  use ArticlesFormParametersTraits;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
              'label' => 'Titre'
            ])
            ->add('period', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'label' => 'Période'
            ])
            ->add('duration', TextType::class, [
              'label' => 'Durée'
            ])
            ->add('categories', EntityType::class, [
              'class' => RealizationCategories::class,
              'choice_label' => 'label',
              'label' => 'Catégories',
              'multiple' => true,
              'required' => true,
              'placeholder' => 'Choisissez des Catégories',
              'query_builder' => function(RealizationCategoriesRepository $rcr) {
                return $rcr->createQueryBuilder('rc')
                  ->orderBy('rc.label', 'ASC');
              },
              'attr' => [
                'data-tselect' => true
              ],
            ])
            ->add('customer', EntityType::class, [
              'class' => Customers::class,
              'choice_label' => 'name',
              'label' => 'Client',
              'multiple' => false,
              'required' => true,
              'query_builder' => function(CustomersRepository $cr) {
                return $cr->createQueryBuilder('c')
                  ->orderBy('c.name', 'ASC');
              },
              'attr' => [
                'data-tselect' => true
              ],
            ])
            ->add('client_view', TextType::class, [
              'label' => 'Avis Client'
            ])
            ->add('photos', FileType::class, [
              'label' => 'Photos du Projet',
              'required' => false,
              'multiple' => true,
              'mapped' => false,
              'attr' => [
                'accept' => 'image/png, image/jpeg, image/webp',
                'data-drop' => 'true',
              ]
            ])
            ->add('context', TextareaType::class, [
              'label' => 'Contexte',
              'attr' => [
                'rows' => 5,
                'maxlength' => $this->maxLengthRealizationContext
              ],
              'help' => "Le contexte de la réalisation ne doit pas contenir plus de ".$this->maxLengthRealizationContext." caractères",
              'constraints' => [
                new Length(
                  max: $this->maxLengthRealizationContext,
                  maxMessage: "Votre message ne doit pas faire plus de ".$this->maxLengthRealizationContext." caractères."
                )
              ]
            ])
            ->add('task', TextareaType::class, [
              'label' => 'Missions',
              'attr' => [
                'rows' => 5,
                'maxlength' => $this->maxLengthRealizationTask
              ],
              'help' => "La mission de la réalisation ne doit pas contenir plus de ".$this->maxLengthRealizationTask." caractères",
              'constraints' => [
                new Length(
                  max: $this->maxLengthRealizationTask,
                  maxMessage: "Votre message ne doit pas faire plus de ".$this->maxLengthRealizationTask." caractères."
                )
              ]
            ])
            ->add('answer', TextareaType::class, [
              'label' => 'Solutions',
              'attr' => [
                'rows' => 5,
                'maxlength' => $this->maxLengthRealizationAnswer
              ],
              'help' => "La mission de la réalisation ne doit pas contenir plus de ".$this->maxLengthRealizationAnswer." caractères",
              'constraints' => [
                new Length(
                  max: $this->maxLengthRealizationAnswer,
                  maxMessage: "Votre message ne doit pas faire plus de ".$this->maxLengthRealizationAnswer." caractères."
                )
              ]
            ])
            // ->add('categories', EntityType::class, [
            //     'class' => RealizationCategories::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Realization::class,
        ]);
    }
}
