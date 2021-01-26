<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Subscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PyramidFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromSeason', EntityType::class, [
                'class' => Season::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'De',
                'error_bubbling' => true
            ])
            ->add('toSeason', EntityType::class, [
                'class' => Season::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'à',
                'error_bubbling' => true
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => array_flip(Subscriber::GENDER),
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le sexe choisi n'est pas une valeur valide"
            ])
            ->add('newSubscriber', CheckboxType::class, [
                'label' => 'Nouveaux uniquement',
                'required' => false
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
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
