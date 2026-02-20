@extends('adminlte::page')

@section('title', 'Test Email Service')

@section('content_header')
    <h1>Test Email Service</h1>
@stop

@section('content')
    <div class="card card-primary max-w-md mx-auto">
        <div class="card-body">
            <form method="GET" action="{{ url('/test-email') }}">
                <div class="form-group">
                    <label for="to">Recipient Email</label>
                    <input type="email" name="to" id="to" class="form-control" value="{{ old('to', config('mail.from.address')) }}" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Send Test Email</button>
            </form>
            @if($status)
                <div class="alert alert-success mt-3">
                    {{ $status }}
                </div>
            @endif
        </div>
    </div>
@stop
