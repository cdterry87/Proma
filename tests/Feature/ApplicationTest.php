<?php

declare(strict_types=1);
use Illuminate\Database\Eloquent\Model;

it('enables strict models outside production', function () {
    expect(Model::preventsLazyLoading())->toBeTrue();
});
