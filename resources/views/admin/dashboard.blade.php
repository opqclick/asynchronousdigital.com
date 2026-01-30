@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Admin Dashboard</h1>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['active_projects'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="small-box-footer">
                    View Projects <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_clients'] }}</h3>
                    <p>Active Clients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.clients.index') }}" class="small-box-footer">
                    View Clients <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_tasks'] }}</h3>
                    <p>Pending Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('admin.tasks.index') }}" class="small-box-footer">
                    View Tasks <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($stats['unpaid_invoices'], 2) }}</h3>
                    <p>Unpaid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.invoices.index') }}" class="small-box-footer">
                    View Invoices <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Task Board - Trello Style -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Board</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- To Do Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">To Do</h5>
                                    <span class="badge badge-secondary float-right">{{ $tasksByStatus['to_do']->count() }}</span>
                                </div>
                                <div class="card-body p-2" style="min-height: 400px;">
                                    @foreach($tasksByStatus['to_do'] as $task)
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">In Progress</h5>
                                    <span class="badge badge-primary float-right">{{ $tasksByStatus['in_progress']->count() }}</span>
                                </div>
                                <div class="card-body p-2" style="min-height: 400px;">
                                    @foreach($tasksByStatus['in_progress'] as $task)
                                        <div class="card mb-2 border-primary">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Review Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Review</h5>
                                    <span class="badge badge-warning float-right">{{ $tasksByStatus['review']->count() }}</span>
                                </div>
                                <div class="card-body p-2" style="min-height: 400px;">
                                    @foreach($tasksByStatus['review'] as $task)
                                        <div class="card mb-2 border-warning">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Done Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Done</h5>
                                    <span class="badge badge-success float-right">{{ $tasksByStatus['done']->count() }}</span>
                                </div>
                                <div class="card-body p-2" style="min-height: 400px;">
                                    @foreach($tasksByStatus['done'] as $task)
                                        <div class="card mb-2 border-success">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-success">
                                                        Completed
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .icon {
            font-size: 60px;
        }
        .card-body {
            overflow-y: auto;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Admin Dashboard loaded');
    </script>
@stop
