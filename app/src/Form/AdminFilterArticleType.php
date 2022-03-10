<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFilterArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'choices' => ['draft' => 'draft', 'online' => 'online', 'reserved' => 'reserved', 'received' => 'received'],
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionner un statut',
                'row_attr' => ['class' => 'flex flex-row items-center'],
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'label',
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionner un tag',
                'row_attr' => ['class' => 'flex flex-row items-center'],
            ])
            ->add('Filtrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'row_attr' => ['class' => 'flex justify-end items-center'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'attr' => ['class' => 'flex flex-row justify-end items-center gap-2'],
        ]);
    }
}
