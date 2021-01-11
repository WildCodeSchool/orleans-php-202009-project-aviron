<?php

namespace App\Service;

use App\Entity\Category;

class ColorCalculator
{
    public const COLORS = [
        '#a65bd7aa' => ['SB', 'S-23', 'T','U'],
        '#6688c3aa' => ['SA', 'S', 'N', 'A'],
        '#37cf9baa' => ['B', 'J9', 'J10', 'J11', 'J12', 'R', 'D', 'D7', 'D30', 'D90'],
        '#d56741aa' => ['J', 'J17', 'J18'],
        '#f2e350aa' => ['M', 'J13', 'J14', 'R', 'I'],
        '#e69138ff' => ['C', 'J15', 'J16'],
        ];
    public function defineColor(string $label): string
    {
        foreach (self::COLORS as $hexaColor => $labels) {
            if (in_array($label, $labels)) {
                $color = $hexaColor;
            }
        }

        return $color ?? '';
    }
}
