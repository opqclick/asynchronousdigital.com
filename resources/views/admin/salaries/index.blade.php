@extends('adminlte::page')

@section('title', 'Salaries')

@section('content_header')
    <h1>Salaries</h1>
@stop

@section('content')
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->id }}</td>
                            <td>{{ $salary->user->name }}</td>
                            <td>{{ $salary->month->format('M Y') }}</td>
                            <td>{{ $salary->project ? $salary->project->name : 'General' }}</td>
                            <td>${{ number_format($salary->base_amount, 2) }}</td>
                            <td>${{ number_format($salary->bonus_amount, 2) }}</td>
                            <td>${{ number_format($salary->deduction_amount, 2) }}</td>
                            <td><strong>${{ number_format($salary->net_amount, 2) }}</strong></td>
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
                                <div class="btn-group">
                                    <a href="{{ route('admin.salaries.show', $salary) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.salaries.destroy', $salary) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
