@extends('adminlte::page')

@section('title', 'Salary Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Salary Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('team-member.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('team-member.salaries.index') }}">Salaries</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Salary Slip - {{ $salary->month->format('F Y') }}</h3>
                    <div class="card-tools">
                        @if($salary->status === 'paid' && !$salary->is_received)
                            <form action="{{ route('team-member.salaries.confirm-received', $salary) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Confirm that you have received this salary payment?')">
                                    <i class="fas fa-check-circle"></i> Confirm Received
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($salary->status === 'paid' && !$salary->is_received)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Action Required:</strong> Please confirm that you have received this salary payment.
                        </div>
                    @endif

                    @if($salary->is_received)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Receipt Confirmed:</strong> You confirmed receiving this salary on {{ $salary->received_at->format('F d, Y \a\t h:i A') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Employee Name:</strong><br>
                            {{ $salary->user->name }}
                        </div>
                        <div class="col-6">
                            <strong>Month:</strong><br>
                            {{ $salary->month->format('F Y') }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Project:</strong><br>
                            {{ $salary->project ? $salary->project->name : 'General' }}
                        </div>
                        <div class="col-6">
                            <strong>Status:</strong><br>
                            @switch($salary->status)
                                @case('pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                @case('paid')
                                    <span class="badge badge-success">Paid</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <hr>

                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Base Salary</td>
                                <td class="text-right">${{ number_format($salary->base_amount, 2) }}</td>
                            </tr>
                            @if($salary->bonus > 0)
                            <tr>
                                <td>Bonus</td>
                                <td class="text-right text-success">+${{ number_format($salary->bonus, 2) }}</td>
                            </tr>
                            @endif
                            @if($salary->deduction > 0)
                            <tr>
                                <td>Deductions</td>
                                <td class="text-right text-danger">-${{ number_format($salary->deduction, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>Total Amount</th>
                                <th class="text-right">${{ number_format($salary->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    @if($salary->payment_date)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Payment Date:</strong> {{ $salary->payment_date->format('F d, Y') }}
                        </div>
                    @endif

                    @if($salary->notes)
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <p>{{ $salary->notes }}</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('team-member.salaries.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Info</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Created:</dt>
                        <dd class="col-sm-6">{{ $salary->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-6">Last Updated:</dt>
                        <dd class="col-sm-6">{{ $salary->updated_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop
