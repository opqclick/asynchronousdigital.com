@extends('adminlte::page')

@section('title', 'Users Management')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Users Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
            <h3 class="card-title">All Users</h3>
            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="usersTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Teams</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'team_member' ? 'primary' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role->name)) }}
                                </span>
                            </td>
                            <td>
                                @if($user->teams->count() > 0)
                                    @foreach($user->teams as $team)
                                        <span class="badge badge-secondary">{{ $team->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No teams</span>
                                @endif
                            </td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.send-invitation', $user) }}" method="POST" 
                                          style="display:inline;" 
                                          onsubmit="return confirm('Send invitation email to {{ $user->email }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Send Invitation">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                          style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "responsive": true,
                "order": [[0, "desc"]]
            });
        });
    </script>
@stop
