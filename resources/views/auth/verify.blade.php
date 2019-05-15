@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-content">
            <h4 class="center-align">Proma</h4>

            <hr/>

            <h6>{{ __('Verify Your Email Address') }}</h6>

            <div>
                @if (session('resent'))
                    <article class="card-panel green lighten-1 white-text">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </article>
                @endif

                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
            </div>
        </div>
    </div>
@endsection
