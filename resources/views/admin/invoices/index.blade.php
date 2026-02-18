@extends('adminlte::page')

@section('title', 'Invoices')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Invoices</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Invoices</h3>
            <div class="card-tools">
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create Invoice
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="invoices-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Project</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->client->user->name }}</td>
                            <td>{{ $invoice->project->name }}</td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td>
                                @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                    <span class="text-danger">{{ $invoice->due_date->format('M d, Y') }}</span>
                                @else
                                    {{ $invoice->due_date->format('M d, Y') }}
                                @endif
                            </td>
                            <td>${{ number_format($invoice->total_amount, 2) }}</td>
                            <td>${{ number_format($invoice->paid_amount, 2) }}</td>
                            <td>${{ number_format($invoice->remaining_balance, 2) }}</td>
                            <td>
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-confirm-message="Are you sure?">
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
            $('#invoices-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
