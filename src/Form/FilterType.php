<?php

namespace App\Form;

use App\Entity\Filter;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromSeason', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'De',
                'error_bubbling' => true
            ])
            ->add('toSeason', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'à',
                'error_bubbling' => true
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'H',
                    'Femme' => 'F'
                ],
                'expanded' => true,
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'error_bubbling' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => Filter::class
        ]);
    }
}
