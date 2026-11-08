<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates a real user with a prompted password', function () {
    $this->artisan('user:create')
        ->expectsQuestion('Name', 'Ada')
        ->expectsQuestion('Email', 'Ada@Example.com')
        ->expectsQuestion('Password', 'a-new-password')
        ->assertSuccessful();

    $user = User::query()->sole();

    expect($user->email)->toBe('ada@example.com')
        ->and($user->fresh()->is_demo)->toBeFalse()
        ->and($user->fresh()->email_notifications)->toBeTrue()
        ->and(Hash::check('a-new-password', $user->password))->toBeTrue();
});

it('defaults new users to not being demo accounts [D-13]', function () {
    expect(User::factory()->create()->fresh()->is_demo)->toBeFalse();
});
