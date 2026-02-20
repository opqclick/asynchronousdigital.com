@extends('adminlte::page')

@section('title', 'Add Salary Record')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Add Salary Record</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.salaries.index') }}">Salaries</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Salary Information</h3>
    </div>
    <form action="{{ route('admin.salaries.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_id">Employee <span class="text-danger">*</span></label>
                        <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id"
                            required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}{{ $user->role ? ' (' . ucfirst(str_replace('_', ' ', $user->role->name)) . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="month">Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control @error('month') is-invalid @enderror" id="month"
                            name="month" value="{{ old('month', date('Y-m')) }}" required>
                        @error('month')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="project_id">Project <small class="text-muted">(optional - leave empty for general
                        salary)</small></label>
                <select class="form-control @error('project_id') is-invalid @enderror" id="project_id"
                    name="project_id">
                    <option value="">General (No specific project)</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="base_amount">Base Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('base_amount') is-invalid @enderror"
                            id="base_amount" name="base_amount" value="{{ old('base_amount') }}" required>
                        @error('base_amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bonus_amount">Bonus Amount ($)</label>
                        <input type="number" step="0.01"
                            class="form-control @error('bonus_amount') is-invalid @enderror" id="bonus_amount"
                            name="bonus_amount" value="{{ old('bonus_amount', 0) }}">
                        @error('bonus_amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="deduction_amount">Deduction Amount ($)</label>
                        <input type="number" step="0.01"
                            class="form-control @error('deduction_amount') is-invalid @enderror" id="deduction_amount"
                            name="deduction_amount" value="{{ old('deduction_amount', 0) }}">
                        @error('deduction_amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                            id="payment_date" name="payment_date" value="{{ old('payment_date') }}">
                        @error('payment_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                    rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="alert alert-info">
                <strong>Net Amount Calculation:</strong><br>
                Net Amount = Base Amount + Bonus Amount - Deduction Amount
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Salary Record
            </button>
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@stop