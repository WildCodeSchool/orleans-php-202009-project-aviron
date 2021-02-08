<?php

namespace App\Validator;

use App\Repository\SeasonRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class AgeCategoryOrderValidator extends ConstraintValidator
{
    private const AGE_CATEGORIES = [
        "J9" => 0,
        "J10" => 1,
        "J11" => 2,
        "J12" => 3,
        "J13" => 4,
        "J14" => 5,
        "J15" => 6,
        "J16" => 7,
        "J17" => 8,
        "J18" => 9,
        "S-23" => 10,
        "S" => 11,
    ];

    public function validate($protocol, Constraint $constraint): void
    {
        if (!is_null($protocol->getfromCategory()) && !is_null($protocol->gettoCategory())) {
            if (
                self::AGE_CATEGORIES[$protocol->getfromCategory()->getLabel()] >
                self::AGE_CATEGORIES[$protocol->gettoCategory()->getLabel()]
            ) {
                $this->context->buildViolation(
                    'La catégorie de fin doit être supérieure à la catégorie de début'
                )
                    ->atPath('toCategory')
                    ->addViolation();
            }
        }
    }
}
