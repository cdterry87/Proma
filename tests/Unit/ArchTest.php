<?php

declare(strict_types=1);

arch('debugging helpers are not used')
    ->expect(['dd', 'dump', 'ddd', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('application code uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('models never disable mass assignment protection', function () {
    foreach (glob(dirname(__DIR__, 2).'/app/Models/{,*/}*.php', GLOB_BRACE) as $file) {
        expect(file_get_contents($file))->not->toContain('$guarded');
    }
});
