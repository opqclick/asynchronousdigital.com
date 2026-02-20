@extends('adminlte::auth.verify')

@section('auth_header', config('app.name'))

@section('auth_body')
    <p class="login-box-msg">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12 mb-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Resend Verification Email') }}
                </button>
            </form>
        </div>
        <div class="col-12">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link btn-block">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
@endsection