@extends('adminlte::page')

@section('title', $project->name)

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $project->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('team-member.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('team-member.projects.index') }}">My Projects</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($project->name, 30) }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- Project Info --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Project Details</h3>
                    <div class="card-tools">
                        @switch($project->status)
                            @case('active') <span class="badge badge-success">Active</span> @break
                            @case('paused') <span class="badge badge-warning">Paused</span> @break
                            @case('completed') <span class="badge badge-info">Completed</span> @break
                            @case('cancelled') <span class="badge badge-danger">Cancelled</span> @break
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    @if($project->description)
                        <p class="text-muted">{{ $project->description }}</p>
                        <hr>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Client</dt>
                                <dd>{{ $project->client->user->name ?? 'N/A' }}</dd>
                                <dt>Project Manager</dt>
                                <dd>{{ $project->projectManager->name ?? 'N/A' }}</dd>
                                <dt>Start Date</dt>
                                <dd>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</dd>
                                <dt>End Date</dt>
                                <dd>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'TBD' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Billing Model</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $project->billing_model)) }}</dd>
                                @if($project->tech_stack)
                                    <dt>Tech Stack</dt>
                                    <dd>
                                        @foreach($project->tech_stack as $tech)
                                            <span class="badge badge-secondary">{{ $tech }}</span>
                                        @endforeach
                                    </dd>
                                @endif
                                @if($project->repository_url)
                                    <dt>Repository</dt>
                                    <dd><a href="{{ $project->repository_url }}" target="_blank" rel="noopener"><i class="fas fa-code-branch mr-1"></i>View Repo</a></dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tasks --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Tasks ({{ $project->tasks->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($task->priority ?? 'normal') }}
                                        </span>
                                    </td>
                                    <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No tasks yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Team Members</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($project->users as $member)
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-user-circle fa-lg mr-2 text-muted"></i>
                                <div>
                                    <strong>{{ $member->name }}</strong>
                                    @if($member->id === auth()->id())
                                        <span class="badge badge-primary ml-1">You</span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No members assigned.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <a href="{{ route('team-member.projects.index') }}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to My Projects
            </a>
        </div>
    </div>
@stop
