@extends('adminlte::page')

@php($isProjectManager = auth()->user()->isProjectManager())

@section('title', $isProjectManager ? 'My Tasks' : 'Tasks')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $isProjectManager ? 'My Tasks' : 'Tasks' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $isProjectManager ? 'My Tasks' : 'Tasks' }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $isProjectManager ? 'Tasks for My Assigned Projects' : 'All Tasks' }}</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.tasks.index') }}" class="d-inline-block mr-2">
                    <select name="assignee_id" class="form-control form-control-sm d-inline-block" style="width: 220px;" onchange="this.form.submit()">
                        <option value="all" {{ ($selectedAssigneeId ?? 'all') === 'all' ? 'selected' : '' }}>All users</option>
                        <option value="me" {{ ($selectedAssigneeId ?? 'all') === 'me' ? 'selected' : '' }}>Assigned to me</option>
                        @foreach($users as $filterUser)
                            <option value="{{ $filterUser->id }}" {{ (string)($selectedAssigneeId ?? 'all') === (string)$filterUser->id ? 'selected' : '' }}>
                                {{ $filterUser->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createTaskModal">
                    <i class="fas fa-plus"></i> Add New Task
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tasks-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->project->name }}</td>
                            <td>
                                @switch($task->status)
                                    @case('to_do')
                                        <span class="badge badge-secondary">To Do</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge badge-info">In Progress</span>
                                        @break
                                    @case('review')
                                        <span class="badge badge-warning">Review</span>
                                        @break
                                    @case('done')
                                        <span class="badge badge-success">Done</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($task->priority)
                                    @case('high')
                                        <span class="badge badge-danger">High</span>
                                        @break
                                    @case('medium')
                                        <span class="badge badge-warning">Medium</span>
                                        @break
                                    @case('low')
                                        <span class="badge badge-info">Low</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($task->users->count() > 0)
                                    @foreach($task->users->take(2) as $user)
                                        <span class="badge badge-primary">{{ $user->name }}</span>
                                    @endforeach
                                    @if($task->users->count() > 2)
                                        <span class="badge badge-secondary">+{{ $task->users->count() - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($task->due_date)
                                    @if($task->due_date->isPast() && $task->status !== 'done')
                                        <span class="text-danger">{{ $task->due_date->format('M d, Y') }}</span>
                                    @else
                                        {{ $task->due_date->format('M d, Y') }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" data-confirm-message="Are you sure you want to delete this task?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createTaskModal" tabindex="-1" role="dialog" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="create-task-form" action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_context" value="task_create_modal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTaskModalLabel">Add New Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="create-task-errors" class="alert alert-danger d-none mb-3"></div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="modal_title">Task Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="modal_title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal_project_id">Project <span class="text-danger">*</span></label>
                                    <select class="form-control @error('project_id') is-invalid @enderror" id="modal_project_id" name="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id', request('project')) == $project->id ? 'selected' : '' }}>
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
                            <label for="modal_description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="modal_description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modal_status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="modal_status" name="status" required>
                                        <option value="to_do" {{ old('status') === 'to_do' ? 'selected' : '' }}>To Do</option>
                                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="review" {{ old('status') === 'review' ? 'selected' : '' }}>Review</option>
                                        <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Done</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modal_priority">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control @error('priority') is-invalid @enderror" id="modal_priority" name="priority" required>
                                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modal_estimated_hours">Estimated Hours</label>
                                    <input type="number" step="0.5" class="form-control @error('estimated_hours') is-invalid @enderror"
                                           id="modal_estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}">
                                    @error('estimated_hours')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modal_due_date">Due Date</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           id="modal_due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="modal_users">Assign to Users</label>
                            <select class="form-control select2 @error('users') is-invalid @enderror" id="modal_users" name="users[]" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('users', [])) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('users.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="modal_attachments">Task Files</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('attachments.*') is-invalid @enderror"
                                       id="modal_attachments" name="attachments[]" multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                                <label class="custom-file-label" for="modal_attachments">Choose files</label>
                            </div>
                            <small class="form-text text-muted">Upload task-related documents, screenshots, or files (Max 10MB per file)</small>
                            @error('attachments.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="create-task-submit-btn">
                            <i class="fas fa-save"></i> Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            const assignableUserIdsByProject = @json($assignableUserIdsByProject);

            $('#tasks-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });

            $('#modal_users').select2({
                theme: 'bootstrap4',
                placeholder: 'Select users',
                dropdownParent: $('#createTaskModal')
            });

            const filterAssignableUsers = function () {
                const projectId = String($('#modal_project_id').val() || '');
                const allowedUserIds = new Set((assignableUserIdsByProject[projectId] || []).map(String));

                $('#modal_users option').each(function () {
                    const optionValue = String($(this).val());
                    const isAllowed = projectId === '' || allowedUserIds.has(optionValue);

                    $(this).prop('disabled', !isAllowed);
                    if (!isAllowed && $(this).prop('selected')) {
                        $(this).prop('selected', false);
                    }
                });

                $('#modal_users').trigger('change.select2');
            };

            $('#modal_project_id').on('change', filterAssignableUsers);
            filterAssignableUsers();

            bsCustomFileInput.init();

            const $createTaskForm = $('#create-task-form');
            const $createTaskErrors = $('#create-task-errors');
            const $createTaskSubmitBtn = $('#create-task-submit-btn');

            const resetCreateTaskValidationState = function () {
                $createTaskErrors.addClass('d-none').empty();
                $createTaskForm.find('.is-invalid').removeClass('is-invalid');
            };

            $createTaskForm.on('submit', function (event) {
                event.preventDefault();
                resetCreateTaskValidationState();

                const formData = new FormData(this);
                $createTaskSubmitBtn.prop('disabled', true);

                $.ajax({
                    url: $createTaskForm.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        $('#createTaskModal').modal('hide');
                        $createTaskForm[0].reset();
                        $('#modal_users').val(null).trigger('change');
                        filterAssignableUsers();

                        if (response && response.message) {
                            alert(response.message);
                        }

                        window.location.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const messages = [];

                            Object.keys(errors).forEach(function (field) {
                                const fieldMessages = errors[field] || [];
                                messages.push(...fieldMessages);

                                const baseField = field.replace(/\..*$/, '');
                                const fieldSelector = '[name="' + baseField + '"]' +
                                    ', [name="' + baseField + '[]"]';
                                $createTaskForm.find(fieldSelector).addClass('is-invalid');
                            });

                            $createTaskErrors.html(messages.join('<br>')).removeClass('d-none');
                            return;
                        }

                        $createTaskErrors
                            .text('Something went wrong while creating the task. Please try again.')
                            .removeClass('d-none');
                    },
                    complete: function () {
                        $createTaskSubmitBtn.prop('disabled', false);
                    }
                });
            });

            @if (old('form_context') === 'task_create_modal' || request('open') === 'create-task')
                $('#createTaskModal').modal('show');
            @endif

            $('#createTaskModal').on('hidden.bs.modal', function () {
                resetCreateTaskValidationState();
            });
        });
    </script>
@stop
