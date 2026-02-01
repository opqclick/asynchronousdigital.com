@extends('adminlte::page')

@section('title', 'Payments')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Payments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payments</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Payments</h3>
            <div class="card-tools">
                <a href="{{ route('admin.payments.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Record Payment
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="payments-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Transaction ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                <a href="{{ route('admin.invoices.show', $payment->invoice) }}">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $payment->invoice->client->user->name }}</td>
                            <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
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
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this payment?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#payments-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
