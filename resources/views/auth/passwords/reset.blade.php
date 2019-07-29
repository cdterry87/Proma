@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <reset :title="'{{ config('app.name') }}'"></reset>
    </form>
@endsection
