@extends('adminlte::page')

@section('title', 'Edit Testimonial')

@section('content_header')
    <h1>Edit Testimonial</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Testimonial Information</h3>
        </div>
        <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST">
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Link to Client (Optional)</label>
                            <select name="client_id" id="client_id" class="form-control select2">
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" data-company="{{ $client->company_name }}"
                                        {{ old('client_id', $testimonial->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->user->name }} - {{ $client->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project_id">Link to Project (Optional)</label>
                            <select name="project_id" id="project_id" class="form-control select2">
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ old('project_id', $testimonial->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->client->company_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_name">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="client_name" class="form-control" 
                                   value="{{ old('client_name', $testimonial->client_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_position">Client Position</label>
                            <input type="text" name="client_position" id="client_position" class="form-control" 
                                   value="{{ old('client_position', $testimonial->client_position) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_company">Company Name</label>
                            <input type="text" name="client_company" id="client_company" class="form-control" 
                                   value="{{ old('client_company', $testimonial->client_company) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_avatar">Avatar URL</label>
                            <input type="url" name="client_avatar" id="client_avatar" class="form-control" 
                                   value="{{ old('client_avatar', $testimonial->client_avatar) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Testimonial Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', $testimonial->content) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rating">Rating <span class="text-danger">*</span></label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="">-- Select Rating --</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="order">Display Order</label>
                            <input type="number" name="order" id="order" class="form-control" 
                                   value="{{ old('order', $testimonial->order) }}" min="0">
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Options</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_featured" id="is_featured" class="custom-control-input" value="1" 
                                       {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_featured">Featured</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_published" id="is_published" class="custom-control-input" value="1" 
                                       {{ old('is_published', $testimonial->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Testimonial
                </button>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        // Auto-fill client name when client is selected
        $('#client_id').on('change', function() {
            if($(this).val()) {
                var clientName = $(this).find('option:selected').text().split(' - ')[0];
                var company = $(this).find('option:selected').data('company');
                $('#client_name').val(clientName);
                $('#client_company').val(company);
            }
        });
    });
</script>
@stop
