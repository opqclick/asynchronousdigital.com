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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                        <tr class="{{ $team->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $team->id }}</td>
                            <td>
                                <strong>{{ $team->name }}</strong>
                                @if($team->trashed())
                                    <span class="badge badge-danger ml-1">Deleted</span>
                                @endif
                            </td>
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
                                    @if($team->trashed() && auth()->user()->isAdmin())
                                        <form action="{{ route('admin.recycle-bin.restore', ['type' => 'teams', 'id' => $team->id]) }}" method="POST" style="display:inline;" data-confirm-message="Restore this team?">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @elseif(!$team->trashed())
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
            $('#teams-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@stop
