@extends('adminlte::page')

@section('title', 'Teams')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Teams</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Teams</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Teams</h3>
            <div class="card-tools">
                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Team
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="teams-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Members</th>
                        <th>Projects</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr>
                            <td>{{ $team->id }}</td>
                            <td><strong>{{ $team->name }}</strong></td>
                            <td>{{ Str::limit($team->description ?? 'N/A', 50) }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $team->users->count() }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $team->projects->count() }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $team->tasks->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.teams.show', $team) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-confirm-message="Are you sure you want to delete this team?">
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
            $('#teams-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
