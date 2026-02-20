@extends('adminlte::auth.password')

@section('auth_header', config('app.name'))

@section('auth_body')
    <p class="login-box-msg">{{ __('Please confirm your password before continuing.') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="input-group mb-3">
            <input id="password" type="password" name="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" required
                autofocus autocomplete="current-password">
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
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Confirm') }}
                </button>
            </div>
        </div>
    </form>
@endsection