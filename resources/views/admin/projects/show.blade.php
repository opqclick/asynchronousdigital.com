@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('adminlte::page')

@section('title', 'Project Details')

@section('content_header')
    <h1>{{ $project->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Project Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Client:</dt>
                        <dd class="col-sm-8">{{ $project->client->user->name }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
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
                        </dd>

                        <dt class="col-sm-4">Budget:</dt>
                        <dd class="col-sm-8">${{ number_format($project->budget, 2) }}</dd>

                        <dt class="col-sm-4">Start Date:</dt>
                        <dd class="col-sm-8">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</dd>

                        <dt class="col-sm-4">End Date:</dt>
                        <dd class="col-sm-8">{{ $project->end_date ? $project->end_date->format('M d, Y') : 'N/A' }}</dd>

                        @if($project->repository_url)
                            <dt class="col-sm-4">Repository:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ $project->repository_url }}" target="_blank">
                                    <i class="fab fa-github"></i> View
                                </a>
                            </dd>
                        @endif
                    </dl>

                    @if($project->description)
                        <hr>
                        <strong><i class="fas fa-file-alt mr-1"></i> Description</strong>
                        <p class="text-muted">{{ $project->description }}</p>
                    @endif

                    @if($project->tech_stack && count($project->tech_stack) > 0)
                        <hr>
                        <strong><i class="fas fa-code mr-1"></i> Tech Stack</strong>
                        <p class="text-muted">
                            @foreach($project->tech_stack as $tech)
                                <span class="badge badge-secondary">{{ $tech }}</span>
                            @endforeach
                        </p>
                    @endif

                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Project
                    </a>
                </div>
            </div>

            <!-- Assigned Teams -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Assigned Teams
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($project->teams->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($project->teams as $team)
                                <li class="list-group-item">
                                    <strong>{{ $team->name }}</strong>
                                    <span class="badge badge-primary float-right">{{ $team->users->count() }} members</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted p-3">No teams assigned to this project.</p>
                    @endif
                </div>
            </div>

            <!-- Project Attachments -->
            @if($project->attachments && count($project->attachments) > 0)
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip"></i> Project Attachments
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($project->attachments as $attachment)
                            <a href="{{ Storage::disk('do_spaces')->url($attachment['path']) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                               target="_blank">
                                <div>
                                    <i class="fas fa-file mr-2"></i>
                                    <strong>{{ $attachment['name'] }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Size: {{ number_format($attachment['size'] / 1024, 2) }} KB
                                        | Uploaded: {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M d, Y') }}
                                    </small>
                                </div>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <!-- Tasks -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Project Tasks
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tasks.create') }}?project={{ $project->id }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($project->tasks->count() > 0)
                        <div class="row">
                            @foreach(['to_do' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
                                <div class="col-md-6 mb-3">
                                    <h5>{{ $label }} <span class="badge badge-secondary">{{ $project->tasks->where('status', $status)->count() }}</span></h5>
                                    @foreach($project->tasks->where('status', $status) as $task)
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <h6 class="mb-1">{{ $task->title }}</h6>
                                                <small class="text-muted">
                                                    @if($task->priority === 'high')
                                                        <span class="badge badge-danger">High</span>
                                                    @elseif($task->priority === 'medium')
                                                        <span class="badge badge-warning">Medium</span>
                                                    @else
                                                        <span class="badge badge-info">Low</span>
                                                    @endif
                                                    @if($task->due_date)
                                                        <span class="ml-2"><i class="far fa-calendar"></i> {{ $task->due_date->format('M d') }}</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No tasks found for this project.</p>
                    @endif
                </div>
            </div>

            <!-- Invoices -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> Invoices
                    </h3>
                </div>
                <div class="card-body">
                    @if($project->invoices->count() > 0)
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            @switch($invoice->status)
                                                @case('draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                    @break
                                                @case('sent')
                                                    <span class="badge badge-info">Sent</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge badge-success">Paid</span>
                                                    @break
                                                @case('overdue')
                                                    <span class="badge badge-danger">Overdue</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-dark">Cancelled</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No invoices found for this project.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
