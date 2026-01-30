@extends('adminlte::page')

@section('title', 'Client Details')

@section('content_header')
    <h1>Client Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="profile-user-img img-fluid img-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; font-size: 48px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>

                    <h3 class="profile-username text-center">{{ $client->user->name }}</h3>

                    <p class="text-muted text-center">
                        @if($client->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right">{{ $client->user->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right">{{ $client->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Company</b> <span class="float-right">{{ $client->company_name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Projects</b> <span class="float-right"><span class="badge badge-info">{{ $client->projects->count() }}</span></span>
                        </li>
                        <li class="list-group-item">
                            <b>Invoices</b> <span class="float-right"><span class="badge badge-warning">{{ $client->invoices->count() }}</span></span>
                        </li>
                    </ul>

                    @if($client->address)
                        <p class="text-muted">
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong><br>
                            {{ $client->address }}
                        </p>
                    @endif

                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Client
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Projects -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder"></i> Projects
                    </h3>
                </div>
                <div class="card-body">
                    @if($client->projects->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Tasks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
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
                                        <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'N/A' }}</td>
                                        <td><span class="badge badge-info">{{ $project->tasks->count() }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No projects found for this client.</p>
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
                    @if($client->invoices->count() > 0)
                        <table class="table table-bordered table-striped">
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
                                @foreach($client->invoices as $invoice)
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
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No invoices found for this client.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
