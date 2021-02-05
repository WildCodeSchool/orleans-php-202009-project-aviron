<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Filter;
use App\Entity\Licence;
use App\Entity\Season;
use App\Entity\Status;
use App\Entity\Subscriber;
use App\Service\StatusCalculator;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD)
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
                'error_bubbling' => true,
                'invalid_message' => "La saison de début choisie n'est pas une valeur valide"
            ])
            ->add('toSeason', EntityType::class, [
                'class' => Season::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'à',
                'error_bubbling' => true,
                'invalid_message' => "La saison de fin choisie n'est pas une valeur valide"
            ])
            ->add('fromAdherent', NumberType::class, [
                'label' => 'De',
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le numéro d'adhérent de début doit être un nombre"
            ])
            ->add('toAdherent', NumberType::class, [
                'label' => 'à',
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le numéro d'adhérent de fin doit être un nombre"
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
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "Le statut choisi n'est pas une valeur valide"
            ])
            ->add('seasonStatus', EntityType::class, [
                'class' => Season::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Saison',
                'error_bubbling' => true,
                'invalid_message' => "La saison choisie pour le statut n'est pas une valeur valide"
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
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Saison',
                'error_bubbling' => true,
                'invalid_message' => "La saison choisie pour le type de licence n'est pas une valeur valide"
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
                'error_bubbling' => true,
                'invalid_message' => "La catégorie de début choisie n'est pas une valeur valide"
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
                'error_bubbling' => true,
                'invalid_message' => "La catégorie de fin choisie n'est pas une valeur valide"
            ])
            ->add('seasonCategory', EntityType::class, [
                'class' => Season::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Saison',
                'error_bubbling' => true,
                'invalid_message' => "La saison choisie pour la catégorie n'est pas une valeur valide"
            ])
            ->add('firstLicence', EntityType::class, [
                'class' => Licence::class,
                'choice_label' => 'acronym',
                'label' => 'En',
                'placeholder' => 'Type de licence',
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' =>
                    "Le type de licence choisi pour la première inscription n'est pas une valeur valide"
            ])
            ->add('firstCategory', EntityType::class, [
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
                'placeholder' => 'Catégorie',
                'label' => 'En',
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "La catégorie choisie pour la première inscription n'est pas une valeur valide"
            ])
            ->add('stillRegistered', CheckboxType::class, [
                'required' => false,
                'label' => 'Toujours inscrit',
                'error_bubbling' => true
            ])
            ->add('duration', NumberType::class, [
                'label' => 'ans',
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => "La durée d'inscription doit être un nombre"
            ])
            ->add('orMore', CheckboxType::class, [
                'required' => false,
                'label' => 'ou plus',
                'error_bubbling' => true
            ])
            ->add('stillAdherent', CheckboxType::class, [
                'required' => false,
                'label' => 'Toujours inscrit',
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
