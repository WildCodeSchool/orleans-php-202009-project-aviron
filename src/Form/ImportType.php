<?php

namespace App\Form;

use App\Entity\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seasonName', TextType::class, [
                'label' => 'Saison :',
                'attr' => [
                    'placeholder' => '2020-2021'
                ]
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier :',
                'attr' => [
                    'placeholder' => 'Sélectionnez un fichier',
                ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Import::class,
        ]);
    }
}
