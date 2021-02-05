<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SeasonName extends Constraint
{
    public string $message = 'Validation du nom de la saison';
}
