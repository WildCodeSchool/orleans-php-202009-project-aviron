<?php

namespace App\Form;

use App\Entity\Import;
use App\Repository\SeasonRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ImportType extends AbstractType
{
    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seasonName', TextType::class, [
                'label' => 'Saison :',
                'attr' => [
                    'placeholder' => '2020-2021'
                ]/*,
                'constraints' => [
                new Assert\Callback(
                    ['callback' => static function (
                        string $seasonName,
                        ExecutionContextInterface $context
                    ) {
                        $seasonYears = explode('-', $seasonName);

                        // Vérification du format des années
                        if (strlen($seasonYears[0]) != 4 || strlen($seasonYears[1]) != 4) {
                            $context->buildViolation('Le nom de saison attendu est "Année de début - Année de
                            fin". ' . $seasonName . ' n\'est pas un nom conforme.')
                                ->addViolation();
                        }

                        // Verifie si les années se suivent dans le nom
                        if ($seasonYears[1] != (int)$seasonYears[0] + 1) {
                            $context->buildViolation('Les deux années doivent se suivre')
                                ->addViolation();
                        }

                        // Vérifie que la saison entrée est bien attenante aux saisons déjà en base de données
                        $firstSeason = $this->seasonRepository->findOneBy([], ['name' => 'ASC'])->getName();
                        $lastSeason = $this->seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();

                        if (
                            $firstSeason !== null &&
                            $lastSeason !== null &&
                            ($seasonYears[1] < substr($firstSeason, 0, 4) ||
                                $seasonYears[0] > substr($lastSeason, 5, 4))
                        ) {
                            $context->buildViolation('La saison ' . $seasonName . ' n\'est pas attenante
                                    aux saisons déjà importées qui vont de ' . $firstSeason . ' à ' . $lastSeason . '.')
                                ->addViolation();
                        }
                    }
                    ]
                )
                ]*/])
            ->add('file', FileType::class, [
                'label' => 'Fichier :'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Import::class,
        ]);
    }
}
