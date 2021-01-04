<?php

namespace App\Form;

use App\Entity\Filter;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label' => 'form.filter.fromSeason'
            ])
            ->add('toSeason', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'form.filter.toSeason'
            ])
            ->add('fromAdherent', NumberType::class, [
                'label' => 'form.filter.fromAdherent',
                'required' => false,
                'error_bubbling' => true
            ])
            ->add('toAdherent', NumberType::class, [
                'label' => 'form.filter.toAdherent',
                'required' => false,
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
