@extends('adminlte::page')

@php($isProjectManager = auth()->user()->isProjectManager())

@section('title', $isProjectManager ? 'Assigned Projects' : 'Projects')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $isProjectManager ? 'Assigned Projects' : 'Projects' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $isProjectManager ? 'Assigned Projects' : 'Projects' }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $isProjectManager ? 'My Assigned Projects' : 'All Projects' }}</h3>
            <div class="card-tools">
                @if(!auth()->user()->isProjectManager())
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Project
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <table id="projects-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Client</th>
                        <th>Project Manager</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->client->user->name }}</td>
                            <td>{{ $project->projectManager?->name ?? 'Unassigned' }}</td>
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
                            <td>${{ number_format($project->budget, 2) }}</td>
                            <td><span class="badge badge-info">{{ $project->tasks->count() }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!auth()->user()->isProjectManager())
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this project?')">
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
            $('#projects-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
