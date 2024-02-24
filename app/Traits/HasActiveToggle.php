<?php

namespace App\Traits;

trait HasActiveToggle
{
    public static function getActiveCodes()
    {
        return collect(
            [
                ['value' => 0,  'label' => 'No'],
                ['value' => 1,  'label' => 'Yes'],
            ]
        );
    }
}
