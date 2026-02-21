@extends('adminlte::page')

@section('title', 'Portfolio Items')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Portfolio Items</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Portfolio Items</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Portfolio Items</h3>
        <div class="card-tools">
            <a href="{{ route('admin.portfolio-items.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Portfolio Item
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="portfolio-items-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($portfolioItems as $item)
                    <tr class="{{ $item->trashed() ? 'table-secondary' : '' }}">
                        <td>{{ $item->display_order }}</td>
                        <td>
                            {{ $item->title }}
                            @if($item->trashed() && auth()->user()->isAdmin())
                                <span class="badge badge-danger ml-1">Deleted</span>
                            @endif
                        </td>
                        <td>{{ $item->client_name ?: '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $item->is_published ? 'success' : 'secondary' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            @if($item->trashed())
                                <form
                                    action="{{ route('admin.recycle-bin.restore', ['type' => 'portfolio-items', 'id' => $item->id]) }}"
                                    method="POST" style="display:inline;" data-confirm-message="Restore this portfolio item?">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.portfolio-items.show', $item) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.portfolio-items.edit', $item) }}" class="btn btn-xs btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.portfolio-items.destroy', $item) }}" method="POST"
                                    style="display:inline;" data-confirm-message="Delete this portfolio item?">
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
    $(document).ready(function () {
        $('#portfolio-items-table').DataTable({
            order: [[0, 'asc']]
        });
    });
</script>
@stop