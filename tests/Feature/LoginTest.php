<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Login;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_correctly()
    {
        $this->get(route('login'))
            ->assertStatus(200);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email', 'password'])
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('home'));
    }
}
