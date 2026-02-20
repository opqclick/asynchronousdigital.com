@extends('adminlte::page')

@section('title', 'My Projects')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>My Projects</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('team-member.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Projects</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Projects I'm Assigned To</h3>
            <div class="card-tools">
                <small class="text-muted">{{ $projects->count() }} project(s)</small>
            </div>
        </div>
        <div class="card-body">
            @if($projects->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                    You are not assigned to any projects yet.
                </div>
            @else
                <div class="row">
                    @foreach($projects as $project)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 card-outline
                                @switch($project->status)
                                    @case('active') card-success @break
                                    @case('paused') card-warning @break
                                    @case('completed') card-info @break
                                    @default card-secondary
                                @endswitch">
                                <div class="card-header">
                                    <h5 class="card-title mb-0 text-truncate" title="{{ $project->name }}">
                                        {{ $project->name }}
                                    </h5>
                                    <div class="card-tools">
                                        @switch($project->status)
                                            @case('active')
                                                <span class="badge badge-success">Active</span>
                                                @break
                                            @case('paused')
                                                <span class="badge badge-warning">Paused</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-info">Completed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-danger">Cancelled</span>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($project->description)
                                        <p class="text-muted small">{{ Str::limit($project->description, 100) }}</p>
                                    @endif
                                    <ul class="list-unstyled mb-0 small">
                                        @if($project->client)
                                            <li class="mb-1">
                                                <i class="fas fa-user-tie mr-1 text-muted"></i>
                                                Client: {{ $project->client->user->name ?? 'N/A' }}
                                            </li>
                                        @endif
                                        @if($project->projectManager)
                                            <li class="mb-1">
                                                <i class="fas fa-user-cog mr-1 text-muted"></i>
                                                PM: {{ $project->projectManager->name }}
                                            </li>
                                        @endif
                                        <li class="mb-1">
                                            <i class="fas fa-tasks mr-1 text-muted"></i>
                                            Tasks: {{ $project->tasks->count() }}
                                        </li>
                                        @if($project->start_date)
                                            <li class="mb-1">
                                                <i class="fas fa-calendar mr-1 text-muted"></i>
                                                {{ $project->start_date->format('M d, Y') }}
                                                @if($project->end_date) → {{ $project->end_date->format('M d, Y') }} @endif
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('team-member.projects.show', $project) }}" class="btn btn-sm btn-primary btn-block">
                                        <i class="fas fa-eye mr-1"></i> View Project
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@stop
