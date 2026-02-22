@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', config('app.name'))

@section('auth_body')
    <p class="login-box-msg">{{ __('Forgot your password? Enter your email and we\'ll send you a reset link.') }}</p>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group mb-3">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('Email') }}" required
                autofocus>
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

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
@endsection