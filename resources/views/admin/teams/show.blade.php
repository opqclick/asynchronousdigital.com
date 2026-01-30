@extends('adminlte::page')

@section('title', 'Team Details')

@section('content_header')
    <h1>{{ $team->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Team Information</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-users mr-1"></i> Team Name</strong>
                    <p class="text-muted">{{ $team->name }}</p>

                    @if($team->description)
                        <hr>
                        <strong><i class="fas fa-file-alt mr-1"></i> Description</strong>
                        <p class="text-muted">{{ $team->description }}</p>
                    @endif

                    <hr>
                    <strong><i class="fas fa-chart-bar mr-1"></i> Statistics</strong>
                    <ul class="list-unstyled">
                        <li><strong>Members:</strong> <span class="badge badge-primary">{{ $team->users->count() }}</span></li>
                        <li><strong>Projects:</strong> <span class="badge badge-info">{{ $team->projects->count() }}</span></li>
                        <li><strong>Tasks:</strong> <span class="badge badge-success">{{ $team->tasks->count() }}</span></li>
                    </ul>

                    <hr>
                    <strong><i class="fas fa-clock mr-1"></i> Created</strong>
                    <p class="text-muted">{{ $team->created_at->format('M d, Y') }}</p>

                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Team
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Team Members -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-friends"></i> Team Members
                    </h3>
                </div>
                <div class="card-body">
                    @if($team->users->count() > 0)
                        <div class="row">
                            @foreach($team->users as $user)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                                    <small class="text-muted">{{ $user->email }}</small><br>
                                                    <span class="badge badge-secondary">{{ ucfirst($user->role->name) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No members assigned to this team.</p>
                    @endif
                </div>
            </div>

            <!-- Assigned Projects -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder"></i> Assigned Projects
                    </h3>
                </div>
                <div class="card-body">
                    @if($team->projects->count() > 0)
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($team->projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>{{ $project->client->user->name }}</td>
                                        <td>
                                            @switch($project->status)
                                                @case('planning')
                                                    <span class="badge badge-secondary">Planning</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge badge-info">In Progress</span>
                                                    @break
                                                @case('on_hold')
                                                    <span class="badge badge-warning">On Hold</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-success">Completed</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>${{ number_format($project->budget, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No projects assigned to this team.</p>
                    @endif
                </div>
            </div>

            <!-- Assigned Tasks -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Assigned Tasks
                    </h3>
                </div>
                <div class="card-body">
                    @if($team->tasks->count() > 0)
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Task Title</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($team->tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->project->name }}</td>
                                        <td>
                                            @switch($task->status)
                                                @case('to_do')
                                                    <span class="badge badge-secondary">To Do</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge badge-info">In Progress</span>
                                                    @break
                                                @case('review')
                                                    <span class="badge badge-warning">Review</span>
                                                    @break
                                                @case('done')
                                                    <span class="badge badge-success">Done</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($task->priority)
                                                @case('high')
                                                    <span class="badge badge-danger">High</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge badge-warning">Medium</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge badge-info">Low</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No tasks assigned to this team.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
