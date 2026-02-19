@extends('adminlte::page')

@section('title', 'Team Content')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Team Content</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Team Content</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Team Content</h3>
            <div class="card-tools">
                <a href="{{ route('admin.team-contents.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Team Member
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="team-contents-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamContents as $item)
                        <tr class="{{ $item->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $item->display_order }}</td>
                            <td>
                                {{ $item->name }}
                                @if($item->trashed() && auth()->user()->isAdmin())
                                    <span class="badge badge-danger ml-1">Deleted</span>
                                @endif
                            </td>
                            <td>{{ $item->role_title ?: 'Team Member' }}</td>
                            <td>
                                <span class="badge badge-{{ $item->is_published ? 'success' : 'secondary' }}">
                                    {{ $item->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                @if($item->trashed())
                                    <form action="{{ route('admin.recycle-bin.restore', ['type' => 'team-contents', 'id' => $item->id]) }}" method="POST" style="display:inline;" data-confirm-message="Restore this team content?">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @elseif(!$item->trashed())
                                    <a href="{{ route('admin.team-contents.show', $item) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.team-contents.edit', $item) }}" class="btn btn-xs btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.team-contents.destroy', $item) }}" method="POST" style="display:inline;" data-confirm-message="Are you sure?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#team-contents-table').DataTable({
            order: [[0, 'asc']]
        });
    });
</script>
@stop
