@extends('adminlte::page')

@section('title', 'Client Dashboard')

@section('content_header')
    <h1>My Projects</h1>
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
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($stats['pending_invoices'], 2) }}</h3>
                    <p>Pending Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($stats['paid_invoices'], 2) }}</h3>
                    <p>Paid Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Projects Overview</h3>
                </div>
                <div class="card-body">
                    @forelse($projects as $project)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    {{ $project->name }}
                                    <span class="badge badge-{{ $project->status == 'active' ? 'success' : ($project->status == 'paused' ? 'warning' : 'secondary') }} float-right">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">{{ $project->description }}</p>
                                
                                @if($project->tech_stack)
                                    <p class="mb-2">
                                        <strong>Tech Stack:</strong>
                                        @foreach($project->tech_stack as $tech)
                                            <span class="badge badge-info">{{ $tech }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <strong>Start Date:</strong><br>
                                        {{ $project->start_date->format('M d, Y') }}
                                    </div>
                                    @if($project->end_date)
                                    <div class="col-md-3">
                                        <strong>End Date:</strong><br>
                                        {{ $project->end_date->format('M d, Y') }}
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <strong>Tasks:</strong><br>
                                        {{ $project->tasks->count() }} tasks
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Progress:</strong><br>
                                        @php
                                            $totalTasks = $project->tasks->count();
                                            $completedTasks = $project->tasks->where('status', 'done')->count();
                                            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task Summary -->
                                @if($project->tasks->count() > 0)
                                    <div class="mt-3">
                                        <h6>Task Status:</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">To Do:</small> <strong>{{ $project->tasks->where('status', 'to_do')->count() }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">In Progress:</small> <strong>{{ $project->tasks->where('status', 'in_progress')->count() }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Review:</small> <strong>{{ $project->tasks->where('status', 'review')->count() }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Done:</small> <strong class="text-success">{{ $project->tasks->where('status', 'done')->count() }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No projects found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    @if($invoices->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Invoices</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Project</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices->take(10) as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->project->name }}</td>
                                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'sent' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop

@section('css')
    <style>
        .small-box .icon {
            font-size: 60px;
        }
    </style>
@stop
