@extends('adminlte::page')

@section('title', 'Recycle Bin')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Recycle Bin</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Recycle Bin</li>
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
            <h3 class="card-title">Deleted Records</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.recycle-bin.index') }}" class="mb-3">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            @foreach($resources as $type => $resource)
                                <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $resource['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Record ID</th>
                            <th>Title</th>
                            <th>Deleted At</th>
                            <th style="width:120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deletedItems as $item)
                            <tr>
                                <td><span class="badge badge-secondary">{{ $item['label'] }}</span></td>
                                <td>#{{ $item['id'] }}</td>
                                <td>{{ $item['title'] }}</td>
                                <td>{{ $item['deleted_at']?->format('M d, Y h:i A') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.recycle-bin.restore', ['type' => $item['type'], 'id' => $item['id']]) }}" data-confirm-message="Restore this record?">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No deleted records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deletedItems->hasPages())
                <div class="mt-3">
                    {{ $deletedItems->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
