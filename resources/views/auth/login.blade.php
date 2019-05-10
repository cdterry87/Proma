@extends('layouts.auth')

@section('content')

    <h4 class="center-align">Proma</h4>

    <hr/>

    <div class="row">
        @include('layouts.errors')
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-field">
            <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
            <label for="email">Email</label>
        </div>

        <div class="input-field">
            <input type="password" id="password" name="password" required>
            <label for="password">Password</label>
        </div>

        <div>
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>{{ __('Remember Me') }}</span>
            </label>
        </div>

        <br/>

        <div class="center-align">
            <button type="submit" class="btn light-blue darken-2">
                {{ __('Login') }}
            </button>

            <a class="btn indigo lighten-1" href="{{ route('register') }}">
                {{ __('Register') }}
            </a>
        </div>

        <br>

        <div class="center-align">
            <a href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        </div>

        <br>

        <hr/>

        <div class="center-align">
            Proma - Simple Project Management &copy; {{ date('Y') }}
        </div>
    </form>

@endsection
