<?php

declare(strict_types=1);

use App\Models\User;

it('shows the user card and logout in the sidebar', function () {
    $user = User::factory()->create(['name' => 'Ada', 'title' => 'Engineer']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Ada')
        ->assertSee('Engineer')
        ->assertSee('Log out')
        ->assertSeeHtml('aria-current="page"');
});

it('escapes stored profile values', function () {
    $user = User::factory()->create(['name' => '<script>alert(1)</script>']);

    $this->actingAs($user)
        ->get('/')
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertSee('<script>alert(1)</script>');
});
