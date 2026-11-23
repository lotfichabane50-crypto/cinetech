<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none';

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du film',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Ex: Interstellar'],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'attr' => ['class' => $inputClass],
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'attr' => ['class' => $inputClass],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Ex: 169'],
            ])
            ->add('rating', NumberType::class, [
                'label' => 'Note presse (sur 5)',
                'required' => false,
                'scale' => 1,
                'attr' => ['class' => $inputClass, 'step' => '0.1', 'placeholder' => 'Ex: 4.5'],
            ])
            ->add('poster', UrlType::class, [
                'label' => 'URL de l\'affiche (facultatif)',
                'required' => false,
                'attr' => ['class' => $inputClass, 'placeholder' => 'https://...'],
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => 'Synopsis (facultatif)',
                'required' => false,
                'attr' => ['class' => $inputClass, 'rows' => 4],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}