@extends('adminlte::page')

@section('title', 'Edit Task')

@section('content_header')
    <h1>Edit Task</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Task Information</h3>
        </div>
        <form action="{{ route('admin.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="project_id">Project <span class="text-danger">*</span></label>
                            <select class="form-control @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="to_do" {{ old('status', $task->status) === 'to_do' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ old('status', $task->status) === 'review' ? 'selected' : '' }}>Review</option>
                                <option value="done" {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priority">Priority <span class="text-danger">*</span></label>
                            <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estimated_hours">Estimated Hours</label>
                            <input type="number" step="0.5" class="form-control @error('estimated_hours') is-invalid @enderror" 
                                   id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $task->estimated_hours) }}">
                            @error('estimated_hours')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="users">Assign to Users</label>
                            <select class="form-control select2 @error('users') is-invalid @enderror" id="users" name="users[]" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('users', $task->users->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="teams">Assign to Teams</label>
                            <select class="form-control select2 @error('teams') is-invalid @enderror" id="teams" name="teams[]" multiple>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ in_array($team->id, old('teams', $task->teams->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teams')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="attachments">Task Files</label>
                    @if($task->attachments && count($task->attachments) > 0)
                        <div class="mb-2">
                            <small class="d-block text-muted">Existing: {{ count($task->attachments) }} file(s)</small>
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('attachments.*') is-invalid @enderror" 
                               id="attachments" name="attachments[]" multiple 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        <label class="custom-file-label" for="attachments">Choose files</label>
                    </div>
                    <small class="form-text text-muted">Upload additional files (Max 10MB per file). New files will be added to existing ones.</small>
                    @error('attachments.*')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Task
                </button>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
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
                placeholder: 'Select...'
            });
            
            // Initialize custom file input
            bsCustomFileInput.init();
        });
    </script>
@stop
