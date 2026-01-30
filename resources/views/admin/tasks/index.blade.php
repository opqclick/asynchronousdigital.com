@extends('adminlte::page')

@section('title', 'Tasks')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tasks</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tasks</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Tasks</h3>
            <div class="card-tools">
                <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Task
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="tasks-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
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
                            <td>
                                @if($task->users->count() > 0)
                                    @foreach($task->users->take(2) as $user)
                                        <span class="badge badge-primary">{{ $user->name }}</span>
                                    @endforeach
                                    @if($task->users->count() > 2)
                                        <span class="badge badge-secondary">+{{ $task->users->count() - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($task->due_date)
                                    @if($task->due_date->isPast() && $task->status !== 'done')
                                        <span class="text-danger">{{ $task->due_date->format('M d, Y') }}</span>
                                    @else
                                        {{ $task->due_date->format('M d, Y') }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this task?')">
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
            $('#tasks-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
