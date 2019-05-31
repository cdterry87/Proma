@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-content">
            <h4 class="center-align">Proma</h4>
            <h6 class="center-align">Simple Project Management</h6>

            <hr/>

            <div class="row">
                @include('layouts.errors')
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-field">
                    <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
                    <label for="email">Email</label>
                </div>

                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>

                <div class="input-field">
                    <input id="password-confirm" type="password" name="password_confirmation" required>
                    <label for="password">Confirm Password</label>
                </div>

                <div class="center-align">
                    <button type="submit" class="btn light-blue darken-2">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
