@extends('adminlte::page')

@section('title', 'My Tasks')

@section('content_header')
    <h1>My Dashboard</h1>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['tasks_due_today'] }}</h3>
                    <p>Due Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['overdue_tasks'] }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed_this_month'] }}</h3>
                    <p>Completed This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_assigned'] }}</h3>
                    <p>Total Assigned</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- My Task Board -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Tasks</h3>
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
                                                        <small class="{{ $task->due_date < now() ? 'text-danger' : 'text-muted' }}">
                                                            {{ $task->due_date->format('M d') }}
                                                        </small>
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
                                                        <small class="{{ $task->due_date < now() ? 'text-danger' : 'text-muted' }}">
                                                            {{ $task->due_date->format('M d') }}
                                                        </small>
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
                                                    @if($task->updated_at)
                                                        <small class="text-muted">{{ $task->updated_at->format('M d') }}</small>
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
    </style>
@stop
