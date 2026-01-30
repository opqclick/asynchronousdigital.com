@extends('adminlte::page')

@section('title', 'Add New Service')

@section('content_header')
    <h1>Add New Service</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Service Information</h3>
        </div>
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">Service Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="order">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                           id="icon" name="icon" value="{{ old('icon') }}" 
                           placeholder="e.g., fas fa-mobile-alt" required>
                    <small class="form-text text-muted">Font Awesome icon class (e.g., fas fa-mobile-alt, fab fa-android)</small>
                    @error('icon')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('short_description') is-invalid @enderror" 
                              id="short_description" name="short_description" rows="2" required>{{ old('short_description') }}</textarea>
                    <small class="form-text text-muted">Brief description for cards (max 500 characters)</small>
                    @error('short_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="full_description">Full Description</label>
                    <textarea class="form-control @error('full_description') is-invalid @enderror" 
                              id="full_description" name="full_description" rows="6">{{ old('full_description') }}</textarea>
                    @error('full_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pricing_model">Pricing Model <span class="text-danger">*</span></label>
                            <select class="form-control @error('pricing_model') is-invalid @enderror" 
                                    id="pricing_model" name="pricing_model" required>
                                <option value="fixed" {{ old('pricing_model') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="hourly" {{ old('pricing_model') == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="custom" {{ old('pricing_model') == 'custom' ? 'selected' : '' }}>Custom Quote</option>
                            </select>
                            @error('pricing_model')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="base_price">Base Price ($)</label>
                            <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" 
                                   id="base_price" name="base_price" value="{{ old('base_price') }}" min="0">
                            @error('base_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price_display">Price Display Text</label>
                            <input type="text" class="form-control @error('price_display') is-invalid @enderror" 
                                   id="price_display" name="price_display" value="{{ old('price_display') }}" 
                                   placeholder="e.g., Starting at $5,000">
                            <small class="form-text text-muted">How price shows on website</small>
                            @error('price_display')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Features</label>
                    <div id="features-container">
                        @if(old('features'))
                            @foreach(old('features') as $index => $feature)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Feature description">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="Feature description">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-success" id="add-feature">
                        <i class="fas fa-plus"></i> Add Feature
                    </button>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" 
                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active (Display on website)</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Service
                </button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#add-feature').click(function() {
        $('#features-container').append(`
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="features[]" placeholder="Feature description">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.input-group').remove();
    });
});
</script>
@stop
