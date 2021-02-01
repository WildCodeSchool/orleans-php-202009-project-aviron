<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SeasonName extends Constraint
{
    public string $message = 'The string contains an illegal character: it can only contain letters or numbers.';
}
