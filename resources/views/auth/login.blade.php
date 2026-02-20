@extends('adminlte::auth.login')

@section('auth_header')
    <a href="{{ route('home') }}">
        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" style="height: 80px; width: auto;">
    </a>
@endsection

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group mb-3">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('Email') }}" required autofocus
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

        <div class="input-group mb-3">
            <input id="password" type="password" name="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" required
                autocomplete="current-password">
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

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">{{ __('Remember Me') }}</label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Log in') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
    @endif
@endsection