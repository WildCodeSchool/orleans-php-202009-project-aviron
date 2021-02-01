<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\PyramidFilter;
use App\Entity\Season;
use App\Entity\Subscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PyramidFilterType extends FilterType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('fromAdherent')
            ->remove('toAdherent')
            ->remove('status')
            ->remove('seasonStatus')
            ->remove('licences')
            ->remove('seasonLicence')
            ->remove('seasonCategory')
            ->remove('firstLicence')
            ->remove('firstCategory')
            ->remove('stillRegistered')
            ->remove('duration')
            ->remove('orMore')
            ->remove('stillAdherent')

            ->add('newSubscriber', CheckboxType::class, [
                'label' => 'Nouveaux uniquement',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PyramidFilter::class
        ]);
    }
}
