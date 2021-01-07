<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Filter;
use App\Entity\Licence;
use App\Entity\Season;
use App\Entity\Subscriber;
use App\Service\StatusCalculator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD)
     */
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
            ->add('fromAdherent', NumberType::class, [
                'label' => 'De',
                'required' => false,
                'error_bubbling' => true
            ])
            ->add('toAdherent', NumberType::class, [
                'label' => 'à',
                'required' => false,
                'error_bubbling' => true
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => Subscriber::GENDER,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le sexe choisi n'est pas une valeur valide"
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    StatusCalculator::NEW,
                    StatusCalculator::TRANSFER,
                    StatusCalculator::RESUMED,
                    StatusCalculator::RENEWAL
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le statut choisi n'est pas une valeur valide"
            ])
            ->add('seasonStatus', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'Saison',
                'error_bubbling' => true
            ])
            ->add('licences', EntityType::class, [
                'class' => Licence::class,
                'choice_label' => 'acronym',
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le type de licence choisi n'est pas une valeur valide"
            ])
            ->add('seasonLicence', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'Saison',
                'error_bubbling' => true
            ])
            ->add('fromCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function ($category) {
                    return $category->getLabel() . ' (' . $category->getOldGroup() . ')';
                },
                'choice_value' => function (?Category $entity) {
                    return $entity ? $entity->getLabel() : '';
                },
                'group_by' => function ($choice, $key, $value) {
                    return $choice->getNewGroup();
                },
                'placeholder' => '',
                'label' => 'De',
                'required' => false,
                'error_bubbling' => true
            ])
            ->add('toCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function ($category) {
                    return $category->getLabel() . ' (' . $category->getOldGroup() . ')';
                },
                'choice_value' => function (?Category $entity) {
                    return $entity ? $entity->getLabel() : '';
                },
                'group_by' => function ($choice, $key, $value) {
                    return $choice->getNewGroup();
                },
                'placeholder' => '',
                'label' => 'à',
                'required' => false,
                'error_bubbling' => true
            ])
            ->add('seasonCategory', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'Saison',
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
