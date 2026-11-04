<?php

declare(strict_types=1);

arch('debugging helpers are not used')
    ->expect(['dd', 'dump', 'ddd', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('application code uses strict types')
    ->expect('App')
    ->toUseStrictTypes();
