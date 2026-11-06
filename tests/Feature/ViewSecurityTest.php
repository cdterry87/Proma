<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('never prints unescaped output in views [D-4]', function () {
    $offenders = collect(File::allFiles(resource_path('views')))
        ->filter(fn ($file) => str_contains($file->getContents(), '{!!'))
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});
