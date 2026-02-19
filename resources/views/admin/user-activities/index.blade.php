@extends('adminlte::page')

@section('title', 'User Activity Log')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>User Activity Log</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">User Activities</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activity Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.user-activities.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>User</label>
                            <select name="user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Action</label>
                            <select name="action" class="form-control">
                                <option value="">All Actions</option>
                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                                <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>View</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Module</label>
                            <select name="model" class="form-control">
                                <option value="">All Modules</option>
                                <option value="User" {{ request('model') == 'User' ? 'selected' : '' }}>Users</option>
                                <option value="Salary" {{ request('model') == 'Salary' ? 'selected' : '' }}>Salaries</option>
                                <option value="Project" {{ request('model') == 'Project' ? 'selected' : '' }}>Projects</option>
                                <option value="Task" {{ request('model') == 'Task' ? 'selected' : '' }}>Tasks</option>
                                <option value="Client" {{ request('model') == 'Client' ? 'selected' : '' }}>Clients</option>
                                <option value="Team" {{ request('model') == 'Team' ? 'selected' : '' }}>Teams</option>
                                <option value="Invoice" {{ request('model') == 'Invoice' ? 'selected' : '' }}>Invoices</option>
                                <option value="Payment" {{ request('model') == 'Payment' ? 'selected' : '' }}>Payments</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activity Log ({{ $activities->total() }} records)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                        <th style="width: 80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->id }}</td>
                            <td>
                                <strong>{{ $activity->user?->name ?? 'Deleted User' }}</strong><br>
                                <small class="text-muted">{{ $activity->user?->email ?? 'N/A' }}</small>
                                @if($activity->trashed())
                                    <br><span class="badge badge-danger mt-1">Deleted</span>
                                @endif
                            </td>
                            <td>
                                @switch($activity->action)
                                    @case('login')
                                        <span class="badge badge-info"><i class="fas fa-sign-in-alt"></i> Login</span>
                                        @break
                                    @case('create')
                                        <span class="badge badge-success"><i class="fas fa-plus"></i> Create</span>
                                        @break
                                    @case('update')
                                        <span class="badge badge-warning"><i class="fas fa-edit"></i> Update</span>
                                        @break
                                    @case('delete')
                                        <span class="badge badge-danger"><i class="fas fa-trash"></i> Delete</span>
                                        @break
                                    @case('view')
                                        <span class="badge badge-secondary"><i class="fas fa-eye"></i> View</span>
                                        @break
                                    @default
                                        <span class="badge badge-light">{{ ucfirst($activity->action) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($activity->model)
                                    <span class="badge badge-light">{{ $activity->model }}</span>
                                    @if($activity->model_id)
                                        <small class="text-muted">#{{ $activity->model_id }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td><code>{{ $activity->ip_address }}</code></td>
                            <td>
                                <small>{{ $activity->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $activity->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.user-activities.show', $activity) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$activity->trashed())
                                    @can('user-activities.edit')
                                        <a href="{{ route('admin.user-activities.edit', $activity) }}"
                                           class="btn btn-sm btn-primary"
                                           title="Edit Activity">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('user-activities.delete')
                                        <form action="{{ route('admin.user-activities.destroy', $activity) }}" method="POST" class="d-inline" data-confirm-message="Delete this activity log?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Activity">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    @can('user-activities.restore')
                                        <form action="{{ route('admin.user-activities.restore', $activity->id) }}" method="POST" class="d-inline" data-confirm-message="Restore this activity log?">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Restore Activity">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No activity records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
            <div class="card-footer clearfix">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@stop
