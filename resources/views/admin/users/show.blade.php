@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('adminlte::page')

@section('title', 'User Details')

@section('content_header')
    <h1>User Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if($user->profile_picture)
                            <img src="{{ Storage::disk('do_spaces')->url($user->profile_picture) }}" 
                                 alt="{{ $user->name }}" 
                                 class="profile-user-img img-fluid img-circle" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px; font-size: 48px; color: white;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>

                    <p class="text-muted text-center">
                        <span class="badge badge-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'team_member' ? 'primary' : 'info') }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role->name)) }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <a class="float-right">{{ $user->phone ?? 'N/A' }}</a>
                        </li>
                        @if($user->date_of_birth)
                        <li class="list-group-item">
                            <b>Date of Birth</b> <a class="float-right">{{ $user->date_of_birth->format('M d, Y') }}</a>
                        </li>
                        @endif
                        @if($user->joining_date)
                        <li class="list-group-item">
                            <b>Joining Date</b> <a class="float-right">{{ $user->joining_date->format('M d, Y') }}</a>
                        </li>
                        @endif
                        <li class="list-group-item">
                            <b>Status</b> 
                            <span class="float-right badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                        @if($user->payment_model)
                        <li class="list-group-item">
                            <b>Payment Model</b> <a class="float-right">{{ ucfirst($user->payment_model) }}</a>
                        </li>
                        @endif
                        @if($user->monthly_salary)
                        <li class="list-group-item">
                            <b>Monthly Salary</b> <a class="float-right">${{ number_format($user->monthly_salary, 2) }}</a>
                        </li>
                        @endif
                        <li class="list-group-item">
                            <b>Member Since</b> <a class="float-right">{{ $user->created_at->format('M d, Y') }}</a>
                        </li>
                    </ul>

                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-block"><b>Edit User</b></a>
                </div>
            </div>

            <!-- Bank Details -->
            @if($user->bank_name || $user->bank_account_number)
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-university"></i> Bank Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        @if($user->bank_name)
                        <dt class="col-sm-4">Bank Name:</dt>
                        <dd class="col-sm-8">{{ $user->bank_name }}</dd>
                        @endif
                        
                        @if($user->bank_account_holder)
                        <dt class="col-sm-4">Account Holder:</dt>
                        <dd class="col-sm-8">{{ $user->bank_account_holder }}</dd>
                        @endif
                        
                        @if($user->bank_account_number)
                        <dt class="col-sm-4">Account Number:</dt>
                        <dd class="col-sm-8">{{ $user->bank_account_number }}</dd>
                        @endif
                        
                        @if($user->bank_routing_number)
                        <dt class="col-sm-4">Routing/IFSC:</dt>
                        <dd class="col-sm-8">{{ $user->bank_routing_number }}</dd>
                        @endif
                        
                        @if($user->bank_swift_code)
                        <dt class="col-sm-4">SWIFT Code:</dt>
                        <dd class="col-sm-8">{{ $user->bank_swift_code }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Documents -->
            @if($user->documents && count($user->documents) > 0)
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt"></i> Documents</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($user->documents as $index => $document)
                            <a href="{{ Storage::disk('do_spaces')->url($document) }}" 
                               class="list-group-item list-group-item-action" 
                               target="_blank">
                                <i class="fas fa-file mr-2"></i>
                                Document {{ $index + 1 }} - {{ basename($document) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Address Card -->
            @if($user->address)
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Address</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $user->address }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <!-- Teams -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teams</h3>
                </div>
                <div class="card-body">
                    @if($user->teams->count() > 0)
                        <div class="row">
                            @foreach($user->teams as $team)
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $team->name }}</span>
                                            <span class="info-box-number">{{ $team->users->count() }} members</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Not assigned to any teams yet.</p>
                    @endif
                </div>
            </div>

            <!-- Tasks -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assigned Tasks</h3>
                </div>
                <div class="card-body">
                    @if($user->tasks->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->project->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No tasks assigned yet.</p>
                    @endif
                </div>
            </div>

            <!-- Salaries -->
            @if($user->salaries->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Salaries</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->salaries->take(5) as $salary)
                                <tr>
                                    <td>{{ $salary->period }}</td>
                                    <td>${{ number_format($salary->amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $salary->status == 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($salary->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $salary->payment_date ? $salary->payment_date->format('M d, Y') : 'Pending' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                  style="display:inline;" 
                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete User
                </button>
            </form>
        </div>
    </div>
@stop
