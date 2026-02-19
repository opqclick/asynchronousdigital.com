@extends('adminlte::page')

@section('title', 'Edit Activity')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Activity #{{ $activity->id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-activities.index') }}">User Activities</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-activities.show', $activity) }}">Details</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Activity Information</h3>
        </div>
        <form method="POST" action="{{ route('admin.user-activities.update', $activity) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>User</label>
                    <input type="text" class="form-control" value="{{ $activity->user->name }} ({{ $activity->user->email }})" disabled>
                </div>

                <div class="form-group">
                    <label for="action">Action <span class="text-danger">*</span></label>
                    <input type="text" id="action" name="action" class="form-control" value="{{ old('action', $activity->action) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="4" maxlength="1000" required>{{ old('description', $activity->description) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.user-activities.show', $activity) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop
