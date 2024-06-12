<?php

namespace App\Traits;

trait WithReferences
{
    public static function getIssuePriorities()
    {
        return collect(
            [
                ['value' => 1,  'label' => 'Lowest', 'class' => 'bg-info text-black'],
                ['value' => 2,  'label' => 'Low', 'class' => 'bg-success text-black'],
                ['value' => 3,  'label' => 'Medium', 'class' => 'bg-gray-400 text-black'],
                ['value' => 4,  'label' => 'High', 'class' => 'bg-warning text-black'],
                ['value' => 5,  'label' => 'Highest', 'class' => 'bg-error text-black'],
            ]
        );
    }
}
