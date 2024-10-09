<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\ArticlesCategories;
use App\Entity\Users;
use App\Repository\ArticlesCategoriesRepository;
use App\Traits\ArticlesFormParametersTraits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ArticlesFormType extends AbstractType
{
  use ArticlesFormParametersTraits;

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
      $builder
          ->add('title', TextType::class, [
            'label' => 'Titre',
            'constraints' => [
              new Length(
                min: 5,
                minMessage: 'Titre trop court. Un minimum de 5 caractères est imposé.'
              )
            ]
          ])
          ->add('summary', TextareaType::class, [
            'label' => 'Extrait/Résumé',
            'attr' => [
              'rows' => 3,
              'maxlength' => $this->maxLengthSummary
            ],
            'help' => "Le résumé de votre article ne doit pas contenir plus de ".$this->maxLengthSummary." caractères",
            'constraints' => [
              new Length(
                max: $this->maxLengthSummary,
                maxMessage: "Votre message ne doit pas faire plus de ".$this->maxLengthSummary." caractères."
              )
            ]
          ])
          ->add('is_important', CheckboxType::class, [
            'label' => 'Article Important',
            'required' => false
          ])
          ->add('categories', EntityType::class, [
            'class' => ArticlesCategories::class,
            'choice_label' => 'label',
            'label' => 'Catégories',
            'multiple' => true,
            'required' => true,
            'placeholder' => 'Choisissez des catégories',
            'query_builder' => function(ArticlesCategoriesRepository $acr) {
              return $acr->createQueryBuilder('ac')
                ->orderBy('ac.label', 'ASC');
            },
            'attr' => [
              'data-tselect' => true
            ],
          ])
          ->add('thumbnail', FileType::class, [
            'label' => 'Glissez la Miniature ici',
            'required' => false,
            'mapped' => false,
            'attr' => [
              'accept' => 'image/png, image/jpeg, image/webp',
              'data-drop' => 'true',
            ]
          ])
          ->add('thumbnail_alt', TextType::class, [
            'label' => 'Nom Alternatif Miniature',
            'required' => false,
            'empty_data' => ''
          ])
          ->add('thumbnail_legend', TextType::class, [
            'label' => 'Légende Miniature',
            'required' => false,
            'empty_data' => ''
          ])
          ->add('data', HiddenType::class, [
            'label' => false,
            'mapped' => false,
            'attr' => [
              'data-content-editor' => 'editor_data'
            ]
          ])
      ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
      $resolver->setDefaults([
          'data_class' => Articles::class,
      ]);
  }
}
