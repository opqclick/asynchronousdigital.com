@extends('adminlte::page')

@section('title', 'Activity Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Activity Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-activities.index') }}">User Activities</a></li>
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
                    <h3 class="card-title">Activity Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.user-activities.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Activity ID:</strong></div>
                        <div class="col-8">{{ $activity->id }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4"><strong>User:</strong></div>
                        <div class="col-8">
                            {{ $activity->user->name }}<br>
                            <small class="text-muted">{{ $activity->user->email }}</small><br>
                            <span class="badge badge-secondary">{{ ucfirst($activity->user->role->name) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4"><strong>Action:</strong></div>
                        <div class="col-8">
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
                        </div>
                    </div>

                    @if($activity->model)
                        <div class="row mb-3">
                            <div class="col-4"><strong>Module:</strong></div>
                            <div class="col-8">
                                <span class="badge badge-light">{{ $activity->model }}</span>
                                @if($activity->model_id)
                                    <small class="text-muted">(ID: {{ $activity->model_id }})</small>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-4"><strong>Description:</strong></div>
                        <div class="col-8">{{ $activity->description }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4"><strong>IP Address:</strong></div>
                        <div class="col-8"><code>{{ $activity->ip_address }}</code></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4"><strong>Date & Time:</strong></div>
                        <div class="col-8">
                            {{ $activity->created_at->format('F d, Y') }}<br>
                            <small class="text-muted">{{ $activity->created_at->format('h:i:s A') }}</small><br>
                            <small class="text-muted">({{ $activity->created_at->diffForHumans() }})</small>
                        </div>
                    </div>

                    @if($activity->user_agent)
                        <div class="row mb-3">
                            <div class="col-4"><strong>User Agent:</strong></div>
                            <div class="col-8">
                                <small class="text-muted">{{ $activity->user_agent }}</small>
                            </div>
                        </div>
                    @endif

                    @if($activity->changes)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Changes Made:</strong>
                                <div class="mt-2">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 30%">Field</th>
                                                <th style="width: 35%">Old Value</th>
                                                <th style="width: 35%">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activity->changes as $field => $change)
                                                <tr>
                                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                                    <td>
                                                        @if(isset($change['old']))
                                                            <span class="text-danger">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($change['new']))
                                                            <span class="text-success">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities by This User</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @php
                            $recentActivities = \App\Models\UserActivity::where('user_id', $activity->user_id)
                                ->where('id', '!=', $activity->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        @forelse($recentActivities as $recent)
                            <li class="list-group-item">
                                <small>
                                    <span class="badge badge-sm badge-{{ $recent->action == 'login' ? 'info' : ($recent->action == 'create' ? 'success' : ($recent->action == 'delete' ? 'danger' : 'warning')) }}">
                                        {{ ucfirst($recent->action) }}
                                    </span>
                                    {{ $recent->description }}<br>
                                    <span class="text-muted">{{ $recent->created_at->diffForHumans() }}</span>
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No other recent activities</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
