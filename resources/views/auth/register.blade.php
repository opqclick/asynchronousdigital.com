@extends('adminlte::auth.register')

@section('auth_header', config('app.name'))

@section('auth_body')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="input-group mb-3">
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Name') }}" required autofocus
                autocomplete="name">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="input-group mb-3">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('Email') }}" required
                autocomplete="username">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input id="password" type="password" name="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" required
                autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="input-group mb-3">
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Register') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    <a href="{{ route('login') }}">{{ __('Already registered?') }}</a>
@endsection