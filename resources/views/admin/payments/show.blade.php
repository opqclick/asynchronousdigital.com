@extends('adminlte::page')

@section('title', 'Payment Details')

@section('content_header')
    <h1>Payment Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Invoice:</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('admin.invoices.show', $payment->invoice) }}">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Client:</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('admin.clients.show', $payment->invoice->client) }}">
                                        {{ $payment->invoice->client->user->name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Project:</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('admin.projects.show', $payment->invoice->project) }}">
                                        {{ $payment->invoice->project->name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Payment Date:</dt>
                                <dd class="col-sm-7">{{ $payment->payment_date->format('M d, Y') }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Amount:</dt>
                                <dd class="col-sm-7">
                                    <h4 class="text-success">${{ number_format($payment->amount, 2) }}</h4>
                                </dd>

                                <dt class="col-sm-5">Method:</dt>
                                <dd class="col-sm-7">
                                    @switch($payment->payment_method)
                                        @case('bank_transfer')
                                            <span class="badge badge-primary">Bank Transfer</span>
                                            @break
                                        @case('credit_card')
                                            <span class="badge badge-info">Credit Card</span>
                                            @break
                                        @case('paypal')
                                            <span class="badge badge-secondary">PayPal</span>
                                            @break
                                        @case('cash')
                                            <span class="badge badge-success">Cash</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                    @endswitch
                                </dd>

                                @if($payment->transaction_id)
                                    <dt class="col-sm-5">Transaction ID:</dt>
                                    <dd class="col-sm-7"><code>{{ $payment->transaction_id }}</code></dd>
                                @endif

                                <dt class="col-sm-5">Recorded:</dt>
                                <dd class="col-sm-7">{{ $payment->created_at->format('M d, Y h:i A') }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($payment->notes)
                        <hr>
                        <strong><i class="fas fa-sticky-note mr-1"></i> Notes</strong>
                        <p class="text-muted">{{ $payment->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Invoice Summary -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Invoice Summary</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Invoice Total:</dt>
                        <dd class="col-sm-8">${{ number_format($payment->invoice->total_amount, 2) }}</dd>

                        <dt class="col-sm-4">Total Paid:</dt>
                        <dd class="col-sm-8 text-success">${{ number_format($payment->invoice->paid_amount, 2) }}</dd>

                        <dt class="col-sm-4">Remaining Balance:</dt>
                        <dd class="col-sm-8 {{ $payment->invoice->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>${{ number_format($payment->invoice->remaining_balance, 2) }}</strong>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($payment->invoice->is_fully_paid)
                                <span class="badge badge-success">Fully Paid</span>
                            @else
                                <span class="badge badge-warning">Partially Paid</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="btn btn-info btn-block">
                        <i class="fas fa-file-invoice"></i> View Invoice
                    </a>
                    <a href="{{ route('admin.payments.create') }}?invoice={{ $payment->invoice_id }}" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> Add Another Payment
                    </a>
                    <button class="btn btn-secondary btn-block" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <hr>
                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" data-confirm-message="Are you sure you want to delete this payment? This will affect the invoice balance.">
                            <i class="fas fa-trash"></i> Delete Payment
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($payment->invoice->payments->sortByDesc('payment_date') as $p)
                            <div class="{{ $p->id === $payment->id ? 'text-primary font-weight-bold' : '' }}">
                                <i class="fas fa-circle {{ $p->id === $payment->id ? 'text-primary' : 'text-secondary' }}"></i>
                                <div class="timeline-item">
                                    <span class="time">{{ $p->payment_date->format('M d, Y') }}</span>
                                    <div class="timeline-body">
                                        ${{ number_format($p->amount, 2) }} 
                                        @if($p->id === $payment->id)
                                            <span class="badge badge-primary">Current</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
