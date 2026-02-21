@extends('adminlte::page')

@section('title', 'Add Portfolio Item')

@section('content_header')
<h1>Add Portfolio Item</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Portfolio Item Details</h3>
    </div>
    <form action="{{ route('admin.portfolio-items.store') }}" method="POST" enctype="multipart/form-data">
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
                <label for="title">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required
                    placeholder="e.g. E-Commerce Platform Redesign">
            </div>

            <div class="form-group">
                <label for="client_name">Client Name</label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                    value="{{ old('client_name') }}" placeholder="e.g. Acme Corp">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"
                    placeholder="Brief overview of the project and your work...">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="image_file">Upload Image</label>
                        <input type="file" name="image_file" id="image_file" class="form-control-file" accept="image/*">
                        <small class="form-text text-muted">Max size: 2MB. Or enter a direct URL below.</small>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Or Image URL</label>
                        <input type="url" name="image_url" id="image_url" class="form-control"
                            value="{{ old('image_url') }}" placeholder="https://...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="project_url">Project URL</label>
                        <input type="url" name="project_url" id="project_url" class="form-control"
                            value="{{ old('project_url') }}" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="tech_tags">Technologies Used</label>
                <input type="text" name="tech_tags" id="tech_tags" class="form-control" value="{{ old('tech_tags') }}"
                    placeholder="Laravel, Vue.js, MySQL (comma-separated)">
                <small class="form-text text-muted">Enter tags separated by commas.</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" name="display_order" id="display_order" class="form-control"
                            value="{{ old('display_order', 0) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Options</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_published" id="is_published" class="custom-control-input"
                                value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_published">Published</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Portfolio Item
            </button>
            <a href="{{ route('admin.portfolio-items.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@stop