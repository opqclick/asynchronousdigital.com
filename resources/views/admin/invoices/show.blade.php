@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
    <h1>Invoice {{ $invoice->invoice_number }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h5>From:</h5>
                            <strong>Asynchronous Digital</strong><br>
                            Digital Agency<br>
                            Email: info@asynchronousdigital.com
                        </div>
                        <div class="col-sm-6 text-right">
                            <h5>Bill To:</h5>
                            <strong>{{ $invoice->client->user->name }}</strong><br>
                            @if($invoice->client->company_name)
                                {{ $invoice->client->company_name }}<br>
                            @endif
                            {{ $invoice->client->user->email }}<br>
                            @if($invoice->client->phone)
                                {{ $invoice->client->phone }}
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <dl class="row">
                                <dt class="col-sm-4">Project:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('admin.projects.show', $invoice->project) }}">
                                        {{ $invoice->project->name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-4">Invoice Date:</dt>
                                <dd class="col-sm-8">{{ $invoice->invoice_date->format('M d, Y') }}</dd>

                                <dt class="col-sm-4">Due Date:</dt>
                                <dd class="col-sm-8">
                                    @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                        <span class="text-danger">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                            <br><small>({{ $invoice->due_date->diffForHumans() }})</small>
                                        </span>
                                    @else
                                        {{ $invoice->due_date->format('M d, Y') }}
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-sm-6 text-right">
                            <h3>Total Amount</h3>
                            <h2 class="text-primary">${{ number_format($invoice->total_amount, 2) }}</h2>
                        </div>
                    </div>

                    @if($invoice->notes)
                        <div class="alert alert-info">
                            <strong>Notes:</strong><br>
                            {{ $invoice->notes }}
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <dl class="row">
                                <dt class="col-sm-5">Total Amount:</dt>
                                <dd class="col-sm-7">${{ number_format($invoice->total_amount, 2) }}</dd>

                                <dt class="col-sm-5">Paid Amount:</dt>
                                <dd class="col-sm-7 text-success">${{ number_format($invoice->paid_amount, 2) }}</dd>

                                <dt class="col-sm-5">Remaining:</dt>
                                <dd class="col-sm-7 text-danger">
                                    <strong>${{ number_format($invoice->remaining_balance, 2) }}</strong>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-sm-6 text-right">
                            @if($invoice->is_fully_paid)
                                <span class="badge badge-success p-3" style="font-size: 1.2em;">
                                    <i class="fas fa-check-circle"></i> PAID IN FULL
                                </span>
                            @else
                                <span class="badge badge-warning p-3" style="font-size: 1.2em;">
                                    PARTIALLY PAID
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i> Payments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.payments.create') }}?invoice={{ $invoice->id }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Payment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($invoice->payments->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Transaction ID</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>
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
                                        </td>
                                        <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No payments recorded for this invoice.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Invoice Status</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @switch($invoice->status)
                                @case('draft')
                                    <span class="badge badge-secondary">Draft</span>
                                    @break
                                @case('sent')
                                    <span class="badge badge-info">Sent</span>
                                    @break
                                @case('paid')
                                    <span class="badge badge-success">Paid</span>
                                    @break
                                @case('overdue')
                                    <span class="badge badge-danger">Overdue</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-dark">Cancelled</span>
                                    @break
                            @endswitch
                        </dd>

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">{{ $invoice->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-5">Updated:</dt>
                        <dd class="col-sm-7">{{ $invoice->updated_at->diffForHumans() }}</dd>
                    </dl>

                    <hr>

                    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
                        <input type="hidden" name="client_id" value="{{ $invoice->client_id }}">
                        <input type="hidden" name="project_id" value="{{ $invoice->project_id }}">
                        <input type="hidden" name="invoice_date" value="{{ $invoice->invoice_date->format('Y-m-d') }}">
                        <input type="hidden" name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}">
                        <input type="hidden" name="total_amount" value="{{ $invoice->total_amount }}">
                        <input type="hidden" name="paid_amount" value="{{ $invoice->paid_amount }}">
                        
                        <div class="form-group">
                            <label>Quick Status Change</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Edit Invoice
                    </a>
                    <a href="{{ route('admin.payments.create') }}?invoice={{ $invoice->id }}" class="btn btn-success btn-block">
                        <i class="fas fa-money-bill-wave"></i> Record Payment
                    </a>
                    <button class="btn btn-info btn-block" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Invoice
                    </button>
                    <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" data-confirm-message="Are you sure?">
                            <i class="fas fa-trash"></i> Delete Invoice
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
