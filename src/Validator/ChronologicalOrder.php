<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ChronologicalOrder extends Constraint
{
    public string $message = 'Vérification de l\'ordre chronologique des saisons';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
