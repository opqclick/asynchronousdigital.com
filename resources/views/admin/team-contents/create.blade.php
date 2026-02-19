@extends('adminlte::page')

@section('title', 'Create Team Content')

@section('content_header')
    <h1>Create Team Content</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Team Member Information</h3>
        </div>
        <form action="{{ route('admin.team-contents.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="role_title">Role / Designation</label>
                    <input type="text" name="role_title" id="role_title" class="form-control" value="{{ old('role_title') }}" placeholder="e.g. Senior Backend Engineer">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="4">{{ old('bio') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="image_url">Profile Image URL</label>
                    <input type="url" name="image_url" id="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://...">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="display_order">Display Order</label>
                            <input type="number" name="display_order" id="display_order" class="form-control" value="{{ old('display_order', 0) }}" min="0">
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Options</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_published" id="is_published" class="custom-control-input" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Team Content
                </button>
                <a href="{{ route('admin.team-contents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop
