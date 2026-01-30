@extends('adminlte::page')

@section('title', 'Salary Details')

@section('content_header')
    <h1>Salary Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Salary Information</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h5>Employee Information:</h5>
                            <strong>{{ $salary->user->name }}</strong><br>
                            {{ $salary->user->email }}<br>
                            <span class="badge badge-secondary">{{ ucfirst($salary->user->role->name) }}</span>
                        </div>
                        <div class="col-sm-6 text-right">
                            <h5>Salary Period:</h5>
                            <strong>{{ $salary->month->format('F Y') }}</strong><br>
                            @if($salary->project)
                                <small class="text-muted">Project: {{ $salary->project->name }}</small>
                            @else
                                <small class="text-muted">General Salary</small>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="50%">Base Amount:</th>
                                        <td class="text-right">${{ number_format($salary->base_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bonus Amount:</th>
                                        <td class="text-right text-success">+${{ number_format($salary->bonus_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deduction Amount:</th>
                                        <td class="text-right text-danger">-${{ number_format($salary->deduction_amount, 2) }}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <th>Net Amount:</th>
                                        <th class="text-right text-primary">
                                            <h4>${{ number_format($salary->net_amount, 2) }}</h4>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
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
                                </dd>

                                @if($salary->payment_date)
                                    <dt class="col-sm-5">Payment Date:</dt>
                                    <dd class="col-sm-7">{{ $salary->payment_date->format('M d, Y') }}</dd>
                                @endif

                                <dt class="col-sm-5">Created:</dt>
                                <dd class="col-sm-7">{{ $salary->created_at->format('M d, Y') }}</dd>

                                <dt class="col-sm-5">Updated:</dt>
                                <dd class="col-sm-7">{{ $salary->updated_at->diffForHumans() }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($salary->notes)
                        <hr>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-sticky-note mr-1"></i> Notes:</strong><br>
                            {{ $salary->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.salaries.update', $salary) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" value="{{ $salary->user_id }}">
                        <input type="hidden" name="month" value="{{ $salary->month->format('Y-m') }}">
                        <input type="hidden" name="project_id" value="{{ $salary->project_id }}">
                        <input type="hidden" name="base_amount" value="{{ $salary->base_amount }}">
                        <input type="hidden" name="bonus_amount" value="{{ $salary->bonus_amount }}">
                        <input type="hidden" name="deduction_amount" value="{{ $salary->deduction_amount }}">
                        <input type="hidden" name="payment_date" value="{{ $salary->payment_date?->format('Y-m-d') }}">
                        
                        <div class="form-group">
                            <label>Change Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="pending" {{ $salary->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $salary->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ $salary->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </form>

                    <hr>

                    <a href="{{ route('admin.users.show', $salary->user) }}" class="btn btn-info btn-block">
                        <i class="fas fa-user"></i> View Employee
                    </a>

                    @if($salary->project)
                        <a href="{{ route('admin.projects.show', $salary->project) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-folder"></i> View Project
                        </a>
                    @endif

                    <button class="btn btn-primary btn-block" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Payslip
                    </button>

                    <hr>

                    <form action="{{ route('admin.salaries.destroy', $salary) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this salary record?')">
                            <i class="fas fa-trash"></i> Delete Record
                        </button>
                    </form>
                </div>
            </div>

            @if($salary->project)
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Project Details</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Name:</dt>
                            <dd class="col-sm-7">{{ $salary->project->name }}</dd>

                            <dt class="col-sm-5">Client:</dt>
                            <dd class="col-sm-7">{{ $salary->project->client->user->name }}</dd>

                            <dt class="col-sm-5">Budget:</dt>
                            <dd class="col-sm-7">${{ number_format($salary->project->budget, 2) }}</dd>

                            <dt class="col-sm-5">Status:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-{{ $salary->project->status === 'completed' ? 'success' : 'info' }}">
                                    {{ ucfirst(str_replace('_', ' ', $salary->project->status)) }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
