<?php

namespace App\Validator;

use App\Repository\SeasonRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ChronologicalOrderValidator extends ConstraintValidator
{
    public function validate($protocol, Constraint $constraint): void
    {
        if (
            substr($protocol->getfromSeason()->getName(), 0, 4) >
            substr($protocol->gettoSeason()->getName(), 0, 4)
        ) {
            $this->context->buildViolation('Pas dans ordre frère')
                ->atPath('toSeason')
                ->addViolation();
        }
    }
}
