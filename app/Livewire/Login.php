<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Login extends Component
{
    public $email, $password, $remember;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.guest');
    }

    public function login()
    {
        $this->validate();

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            return redirect()->intended(route('home'));
        }
    }
}
