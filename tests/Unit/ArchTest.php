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

arch('no package that ships UI or CSS is installed', function () {
    $root = dirname(__DIR__, 2);
    $packages = [
        ...array_column(json_decode(file_get_contents($root.'/composer.lock'), true)['packages'], 'name'),
        ...array_keys(json_decode(file_get_contents($root.'/package-lock.json'), true)['packages']),
    ];
    $banned = '/(^|\/)(tailwindcss|daisyui|bootstrap|alpinejs)$|powergrid|livewire\/flux/';

    expect(preg_grep($banned, $packages))->toBe([]);
});

arch('dead dependencies stay out [D-33]', function () {
    $composer = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true);

    expect($composer['require'])->not->toHaveKeys(['laravel/sanctum', 'laravel/jetstream', 'laravel/breeze']);
});
