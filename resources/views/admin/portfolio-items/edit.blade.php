@extends('adminlte::page')

@section('title', 'Edit Portfolio Item')

@section('content_header')
<h1>Edit Portfolio Item</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Update Portfolio Item</h3>
    </div>
    <form action="{{ route('admin.portfolio-items.update', $portfolioItem) }}" method="POST">
        @csrf
        @method('PUT')
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
                <input type="text" name="title" id="title" class="form-control"
                    value="{{ old('title', $portfolioItem->title) }}" required>
            </div>

            <div class="form-group">
                <label for="client_name">Client Name</label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                    value="{{ old('client_name', $portfolioItem->client_name) }}">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"
                    rows="4">{{ old('description', $portfolioItem->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="url" name="image_url" id="image_url" class="form-control"
                            value="{{ old('image_url', $portfolioItem->image_url) }}" placeholder="https://...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="project_url">Project URL</label>
                        <input type="url" name="project_url" id="project_url" class="form-control"
                            value="{{ old('project_url', $portfolioItem->project_url) }}" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="tech_tags">Technologies Used</label>
                <input type="text" name="tech_tags" id="tech_tags" class="form-control"
                    value="{{ old('tech_tags', $portfolioItem->tech_tags ? implode(', ', $portfolioItem->tech_tags) : '') }}"
                    placeholder="Laravel, Vue.js, MySQL (comma-separated)">
                <small class="form-text text-muted">Enter tags separated by commas.</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" name="display_order" id="display_order" class="form-control"
                            value="{{ old('display_order', $portfolioItem->display_order) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Options</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_published" id="is_published" class="custom-control-input"
                                value="1" {{ old('is_published', $portfolioItem->is_published) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_published">Published</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Portfolio Item
            </button>
            <a href="{{ route('admin.portfolio-items.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@stop