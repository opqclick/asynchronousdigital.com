@extends('adminlte::page')

@section('title', 'Salaries')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Salaries</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Salaries</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Salary Records</h3>
            <div class="card-tools">
                <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Salary Record
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.salaries.index') }}" class="mb-3">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="month">Filter by Month</label>
                            <input type="month"
                                   id="month"
                                   name="month"
                                   class="form-control"
                                   value="{{ $filterMonth ?? '' }}"
                                   onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-list mr-1"></i> Show All
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0 ml-auto">
                        <small class="text-muted">
                            Showing <strong>{{ $salaries->count() }}</strong> record(s)
                            @if($filterMonth) for <strong>{{ \Carbon\Carbon::parse($filterMonth)->format('F Y') }}</strong>@endif
                        </small>
                    </div>
                </div>
            </form>

            <table id="salaries-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Project</th>
                        <th>Base Amount</th>
                        <th>Bonus</th>
                        <th>Deductions</th>
                        <th>Net Amount</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                        <tr class="{{ $salary->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $salary->id }}</td>
                            <td>
                                {{ $salary->user?->name ?? 'Deleted User' }}
                                @if($salary->trashed())
                                    <span class="badge badge-danger ml-1">Deleted</span>
                                @endif
                            </td>
                            <td>{{ $salary->month->format('M Y') }}</td>
                            <td>{{ $salary->project ? $salary->project->name : 'General' }}</td>
                            <td>${{ number_format($salary->base_amount, 2) }}</td>
                            <td>${{ number_format($salary->bonus, 2) }}</td>
                            <td>${{ number_format($salary->deduction, 2) }}</td>
                            <td><strong>${{ number_format($salary->total_amount, 2) }}</strong></td>
                            <td>
                                @switch($salary->status)
                                    @case('pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @break
                                    @case('paid')
                                        <span class="badge badge-success">Paid</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($salary->is_received)
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Confirmed</span>
                                    <br><small class="text-muted">{{ $salary->received_at->format('M d, Y') }}</small>
                                @elseif($salary->status === 'paid')
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Awaiting</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if($salary->trashed() && auth()->user()->isAdmin())
                                        <form action="{{ route('admin.recycle-bin.restore', ['type' => 'salaries', 'id' => $salary->id]) }}" method="POST" style="display:inline;" data-confirm-message="Restore this salary record?">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @elseif(!$salary->trashed())
                                        <a href="{{ route('admin.salaries.show', $salary) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.salaries.destroy', $salary) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-confirm-message="Are you sure?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#salaries-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
