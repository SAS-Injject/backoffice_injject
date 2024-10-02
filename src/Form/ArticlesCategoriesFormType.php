<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\ArticlesCategories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ArticlesCategoriesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
              'label' => "Titre"
            ])
            ->add('account', TextType::class, [
              'label' => "Description",
              'constraints' => [
                new Length(
                  max: 150,
                  minMessage: 'Titre trop long. Un maximum de 150 caractères est imposé.'
                )
            ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticlesCategories::class,
        ]);
    }
}
