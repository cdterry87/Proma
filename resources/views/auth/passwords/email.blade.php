@extends('layouts.auth')

@section('content')

    <h4 class="center-align">Proma</h4>

    <hr/>

    <div class="row">
        @include('layouts.errors')
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-field">
            <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
            <label for="email">Email</label>
        </div>

        <br/>

        <div class="center-align">
            <button type="submit" class="btn light-blue darken-2">
                {{ __('Send Password Reset Link') }}
            </button>
        </div>

        <br>

        <div class="center-align">
            <a href="{{ route('login') }}">
                {{ __('Return to Login') }}
            </a>
        </div>

        <br>

        <hr/>

        <div class="center-align">
            Proma - Simple Project Management &copy; {{ date('Y') }}
        </div>
    </form>

@endsection
