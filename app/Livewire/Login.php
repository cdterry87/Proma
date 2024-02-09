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

        // User must be active to login
        $user = User::where('email', $this->email)->first();
        if ($user && !$user->active) {
            session()->flash('error', 'Your account is not active. Please contact your administrator.');
            return;
        }

        // Attempt login
        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            return redirect()->intended(route('home'));
        }

        session()->flash('error', 'Email or password is incorrect.');
    }
}
