<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AgeCategoryOrder extends Constraint
{
    public string $message = 'Vérification de l\'ordre des catégories d\'âge';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
