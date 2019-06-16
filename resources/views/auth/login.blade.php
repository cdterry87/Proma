@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-content">
            <h4 class="center-align">Proma</h4>
            <h6 class="center-align">Simple Project Management</h6>

            <hr/>

            <div class="row">
                @include('layouts.demo')
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

                    <a class="btn indigo lighten-1 white-text" href="{{ route('register') }}">
                        {{ __('Register') }}
                    </a>
                </div>

                <br>

                <div class="center-align">
                    <a class="blue-text" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>

                <hr/>

                <div class="center-align small">
                    {{ config('app.name', 'Laravel') }} &copy; {{ date('Y') }}
                </div>
            </form>
        </div>
    </div>
@endsection
