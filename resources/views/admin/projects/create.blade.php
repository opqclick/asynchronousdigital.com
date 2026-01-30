@extends('adminlte::page')

@section('title', 'Add New Project')

@section('content_header')
    <h1>Add New Project</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Project Information</h3>
        </div>
        <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Client <span class="text-danger">*</span></label>
                            <select class="form-control @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->user->name }} - {{ $client->company_name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="billing_model">Billing Model <span class="text-danger">*</span></label>
                            <select class="form-control @error('billing_model') is-invalid @enderror" id="billing_model" name="billing_model" required>
                                <option value="fixed_price" {{ old('billing_model', 'fixed_price') === 'fixed_price' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="monthly" {{ old('billing_model') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="task_based" {{ old('billing_model') === 'task_based' ? 'selected' : '' }}>Task Based</option>
                            </select>
                            @error('billing_model')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project_value">Project Value ($)</label>
                            <input type="number" step="0.01" class="form-control @error('project_value') is-invalid @enderror" 
                                   id="project_value" name="project_value" value="{{ old('project_value') }}">
                            @error('project_value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="repository_url">Repository URL</label>
                    <input type="url" class="form-control @error('repository_url') is-invalid @enderror" 
                           id="repository_url" name="repository_url" value="{{ old('repository_url') }}">
                    @error('repository_url')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tech_stack">Tech Stack <small class="text-muted">(comma-separated)</small></label>
                    <input type="text" class="form-control @error('tech_stack') is-invalid @enderror" 
                           id="tech_stack" name="tech_stack" value="{{ old('tech_stack') }}" 
                           placeholder="e.g., Laravel, Vue.js, MySQL, Redis">
                    <small class="form-text text-muted">Enter technologies separated by commas</small>
                    @error('tech_stack')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="teams">Assign Teams</label>
                    <select class="form-control select2 @error('teams') is-invalid @enderror" id="teams" name="teams[]" multiple>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ in_array($team->id, old('teams', [])) ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teams')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="attachments">Project Files</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('attachments.*') is-invalid @enderror" 
                               id="attachments" name="attachments[]" multiple 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        <label class="custom-file-label" for="attachments">Choose files</label>
                    </div>
                    <small class="form-text text-muted">Upload project documents, images, or compressed files (Max 10MB per file). Files will be stored in S3.</small>
                    @error('attachments.*')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Project
                </button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select teams'
            });
            
            // Initialize custom file input
            bsCustomFileInput.init();
        });
    </script>
@stop
