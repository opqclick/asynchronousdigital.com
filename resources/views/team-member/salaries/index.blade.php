@extends('adminlte::page')

@section('title', 'My Salaries')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>My Salaries</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('team-member.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Salaries</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Salary Records</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('team-member.salaries.index') }}" class="mb-3">
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
                                <a href="{{ route('team-member.salaries.index') }}" class="btn btn-secondary btn-sm">
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
                        <th>Month</th>
                        <th>Project</th>
                        <th>Base Amount</th>
                        <th>Bonus</th>
                        <th>Deduction</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Payment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                        <tr>
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
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $salary->payment_date ? $salary->payment_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('team-member.salaries.show', $salary) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No salary records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#salaries-table').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
