<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('urls', CollectionType::class, [
                'entry_type' => UrlType::class,
                'allow_add' => true,
            ])
            ->add('title')
            ->add('description', CKEditorType::class)
            ->add('attachment', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('price', MoneyType::class)
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'label',
            ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
