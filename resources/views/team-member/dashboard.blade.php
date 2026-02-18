@extends('adminlte::page')

@section('title', 'My Tasks')

@section('content_header')
    <h1>My Dashboard</h1>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['tasks_due_today'] }}</h3>
                    <p>Due Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['overdue_tasks'] }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed_this_month'] }}</h3>
                    <p>Completed This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_assigned'] }}</h3>
                    <p>Total Assigned</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Created Tasks</h3>
                    <div class="card-tools">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="created-unassigned-only-toggle">
                            <label class="custom-control-label" for="created-unassigned-only-toggle">Show only not assigned to me</label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($myCreatedTasks->isNotEmpty())
                        <table class="table table-striped mb-0" id="my-created-tasks-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myCreatedTasks as $createdTask)
                                    <tr class="created-task-row" data-assigned-to-me="{{ $createdTask->users->contains('id', auth()->id()) ? '1' : '0' }}">
                                        <td>{{ $createdTask->title }}</td>
                                        <td>{{ $createdTask->project?->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $createdTask->status === 'done' ? 'success' : ($createdTask->status === 'in_progress' ? 'primary' : ($createdTask->status === 'review' ? 'warning' : 'secondary')) }}">
                                                {{ ucwords(str_replace('_', ' ', $createdTask->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $createdTask->priority === 'high' ? 'warning' : ($createdTask->priority === 'medium' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($createdTask->priority) }}
                                            </span>
                                        </td>
                                        <td>{{ $createdTask->due_date?->format('M d, Y') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="created-task-empty-filtered" class="p-3 text-muted d-none">No created tasks match the current filter.</div>
                    @else
                        <div class="p-3 text-muted">No tasks created by you yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- My Task Board -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Tasks</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('team-member.dashboard') }}" class="d-inline-block mr-2">
                            <select name="task_filter" class="form-control form-control-sm d-inline-block" style="width: 180px;" onchange="this.form.submit()">
                                <option value="assigned_to_me" {{ ($taskFilter ?? 'assigned_to_me') === 'assigned_to_me' ? 'selected' : '' }}>Assigned to me</option>
                                <option value="all_project_tasks" {{ ($taskFilter ?? 'assigned_to_me') === 'all_project_tasks' ? 'selected' : '' }}>All project tasks</option>
                            </select>
                        </form>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createTeamTaskModal">
                            <i class="fas fa-plus"></i> Create Task
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- To Do Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">To Do</h5>
                                    <span class="badge badge-secondary float-right">{{ $tasksByStatus['to_do']->count() }}</span>
                                </div>
                                <div class="card-body p-2 task-column" data-status="to_do" style="min-height: 400px;">
                                    @foreach($tasksByStatus['to_do'] as $task)
                                        @php($canMoveTask = $task->users->contains('id', auth()->id()))
                                        <div class="card mb-2 task-card {{ $canMoveTask ? '' : 'task-card-readonly' }}" draggable="{{ $canMoveTask ? 'true' : 'false' }}" data-can-move="{{ $canMoveTask ? '1' : '0' }}" data-task-id="{{ $task->id }}" style="cursor: {{ $canMoveTask ? 'move' : 'not-allowed' }};">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="{{ $task->due_date < now() ? 'text-danger' : 'text-muted' }}">
                                                            {{ $task->due_date->format('M d') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center mt-2" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $assignee)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1"
                                                                     style="width: 24px; height: 24px; font-size: 10px;"
                                                                     title="{{ $assignee->name }}">
                                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                                </div>
                                                            @endforeach
                                                            @if($task->users->count() > 3)
                                                                <small class="text-muted">+{{ $task->users->count() - 3 }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">In Progress</h5>
                                    <span class="badge badge-primary float-right">{{ $tasksByStatus['in_progress']->count() }}</span>
                                </div>
                                <div class="card-body p-2 task-column" data-status="in_progress" style="min-height: 400px;">
                                    @foreach($tasksByStatus['in_progress'] as $task)
                                        @php($canMoveTask = $task->users->contains('id', auth()->id()))
                                        <div class="card mb-2 border-primary task-card {{ $canMoveTask ? '' : 'task-card-readonly' }}" draggable="{{ $canMoveTask ? 'true' : 'false' }}" data-can-move="{{ $canMoveTask ? '1' : '0' }}" data-task-id="{{ $task->id }}" style="cursor: {{ $canMoveTask ? 'move' : 'not-allowed' }};">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="{{ $task->due_date < now() ? 'text-danger' : 'text-muted' }}">
                                                            {{ $task->due_date->format('M d') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center mt-2" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $assignee)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1"
                                                                     style="width: 24px; height: 24px; font-size: 10px;"
                                                                     title="{{ $assignee->name }}">
                                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                                </div>
                                                            @endforeach
                                                            @if($task->users->count() > 3)
                                                                <small class="text-muted">+{{ $task->users->count() - 3 }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Review Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Review</h5>
                                    <span class="badge badge-warning float-right">{{ $tasksByStatus['review']->count() }}</span>
                                </div>
                                <div class="card-body p-2 task-column" data-status="review" style="min-height: 400px;">
                                    @foreach($tasksByStatus['review'] as $task)
                                        @php($canMoveTask = $task->users->contains('id', auth()->id()))
                                        <div class="card mb-2 border-warning task-card {{ $canMoveTask ? '' : 'task-card-readonly' }}" draggable="{{ $canMoveTask ? 'true' : 'false' }}" data-can-move="{{ $canMoveTask ? '1' : '0' }}" data-task-id="{{ $task->id }}" style="cursor: {{ $canMoveTask ? 'move' : 'not-allowed' }};">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center mt-2" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $assignee)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1"
                                                                     style="width: 24px; height: 24px; font-size: 10px;"
                                                                     title="{{ $assignee->name }}">
                                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                                </div>
                                                            @endforeach
                                                            @if($task->users->count() > 3)
                                                                <small class="text-muted">+{{ $task->users->count() - 3 }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Done Column -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Done</h5>
                                    <span class="badge badge-success float-right">{{ $tasksByStatus['done']->count() }}</span>
                                </div>
                                <div class="card-body p-2 task-column" data-status="done" style="min-height: 400px;">
                                    @foreach($tasksByStatus['done'] as $task)
                                        @php($canMoveTask = $task->users->contains('id', auth()->id()))
                                        <div class="card mb-2 border-success task-card {{ $canMoveTask ? '' : 'task-card-readonly' }}" draggable="{{ $canMoveTask ? 'true' : 'false' }}" data-can-move="{{ $canMoveTask ? '1' : '0' }}" data-task-id="{{ $task->id }}" style="cursor: {{ $canMoveTask ? 'move' : 'not-allowed' }};">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center mt-2" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $assignee)
                                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-1"
                                                                     style="width: 24px; height: 24px; font-size: 10px;"
                                                                     title="{{ $assignee->name }}">
                                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                                </div>
                                                            @endforeach
                                                            @if($task->users->count() > 3)
                                                                <small class="text-muted">+{{ $task->users->count() - 3 }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTeamTaskModal" tabindex="-1" role="dialog" aria-labelledby="createTeamTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="team-create-task-form" action="{{ route('team-member.tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTeamTaskModalLabel">Create Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="team-create-task-errors" class="alert alert-danger d-none mb-3"></div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="team_modal_title">Task Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="team_modal_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team_modal_project_id">Project <span class="text-danger">*</span></label>
                                    <select class="form-control" id="team_modal_project_id" name="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="team_modal_description">Description</label>
                            <textarea class="form-control" id="team_modal_description" name="description" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team_modal_priority">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control" id="team_modal_priority" name="priority" required>
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team_modal_estimated_hours">Estimated Hours</label>
                                    <input type="number" step="0.5" class="form-control" id="team_modal_estimated_hours" name="estimated_hours">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team_modal_due_date">Due Date</label>
                                    <input type="date" class="form-control" id="team_modal_due_date" name="due_date">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="team_modal_attachments">Task Files</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="team_modal_attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                                <label class="custom-file-label" for="team_modal_attachments">Choose files</label>
                            </div>
                            <small class="form-text text-muted">Max 10MB per file</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="team-create-task-submit-btn">
                            <i class="fas fa-save"></i> Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDetailModalTitle">Task Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="taskDetailContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .icon {
            font-size: 60px;
        }
        .task-card {
            transition: opacity 0.3s, transform 0.1s;
        }
        .task-card:hover {
            transform: scale(1.02);
        }
        .task-card.dragging {
            opacity: 0.5;
        }
        .task-card-readonly {
            opacity: 0.9;
        }
        .task-column.drag-over {
            background-color: #e9ecef;
            border: 2px dashed #007bff;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            let draggedTask = null;

            bsCustomFileInput.init();

            const $teamCreateTaskForm = $('#team-create-task-form');
            const $teamCreateTaskErrors = $('#team-create-task-errors');
            const $teamCreateTaskSubmitBtn = $('#team-create-task-submit-btn');

            const resetTeamCreateTaskValidation = function () {
                $teamCreateTaskErrors.addClass('d-none').empty();
                $teamCreateTaskForm.find('.is-invalid').removeClass('is-invalid');
            };

            $teamCreateTaskForm.on('submit', function (event) {
                event.preventDefault();
                resetTeamCreateTaskValidation();

                const formData = new FormData(this);
                $teamCreateTaskSubmitBtn.prop('disabled', true);

                $.ajax({
                    url: $teamCreateTaskForm.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        $('#createTeamTaskModal').modal('hide');
                        $teamCreateTaskForm[0].reset();

                        if (response && response.message) {
                            toastr.success(response.message);
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
                                const selector = '[name="' + baseField + '"]' + ', [name="' + baseField + '[]"]';
                                $teamCreateTaskForm.find(selector).addClass('is-invalid');
                            });

                            $teamCreateTaskErrors.html(messages.join('<br>')).removeClass('d-none');
                            return;
                        }

                        $teamCreateTaskErrors
                            .text('Something went wrong while creating the task. Please try again.')
                            .removeClass('d-none');
                    },
                    complete: function () {
                        $teamCreateTaskSubmitBtn.prop('disabled', false);
                    }
                });
            });

            $('#createTeamTaskModal').on('hidden.bs.modal', function () {
                resetTeamCreateTaskValidation();
            });

            $('#created-unassigned-only-toggle').on('change', function() {
                const onlyUnassigned = $(this).is(':checked');
                let visibleCount = 0;

                $('.created-task-row').each(function() {
                    const assignedToMe = $(this).data('assigned-to-me') === 1 || $(this).data('assigned-to-me') === '1';
                    const show = !onlyUnassigned || !assignedToMe;
                    $(this).toggle(show);
                    if (show) {
                        visibleCount++;
                    }
                });

                $('#created-task-empty-filtered').toggleClass('d-none', visibleCount > 0 || !onlyUnassigned);
            });

            $('.task-card').on('dragstart', function(e) {
                const canMove = String($(this).data('can-move')) === '1';
                if (!canMove) {
                    e.preventDefault();
                    toastr.error('You can only move tasks assigned to you.');
                    return false;
                }

                draggedTask = $(this);
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/html', $(this).html());
            });

            $('.task-card').on('dragend', function() {
                $(this).removeClass('dragging');
            });

            $('.task-column').on('dragover', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                $(this).addClass('drag-over');
                return false;
            });

            $('.task-column').on('dragleave', function() {
                $(this).removeClass('drag-over');
            });

            $('.task-column').on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');

                if (!draggedTask) {
                    return false;
                }

                const canMove = String(draggedTask.data('can-move')) === '1';
                if (!canMove) {
                    toastr.error('You can only move tasks assigned to you.');
                    draggedTask = null;
                    return false;
                }

                const taskId = draggedTask.data('task-id');
                const newStatus = $(this).data('status');
                const oldStatus = draggedTask.closest('.task-column').data('status');

                if (oldStatus === newStatus) {
                    draggedTask = null;
                    return false;
                }

                const $targetColumn = $(this);
                $targetColumn.append(draggedTask);

                $.ajax({
                    url: '/team/tasks/' + taskId + '/update-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function() {
                        updateBadgeCounts();
                        toastr.success('Task status updated');
                    },
                    error: function(xhr) {
                        const message = xhr?.responseJSON?.message || 'Failed to update task status';
                        toastr.error(message);
                        location.reload();
                    }
                });

                draggedTask = null;
                return false;
            });

            $(document).on('click', '.task-card', function(e) {
                if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                    const $taskCard = $(this);
                    const taskId = $taskCard.data('task-id');
                    const canInteract = String($taskCard.data('can-move')) === '1';
                    showTaskDetails(taskId, canInteract);
                }
            });

            function showTaskDetails(taskId, canInteract = true) {
                $('#taskDetailModal').modal('show');
                const taskTitle = $('.task-card[data-task-id="' + taskId + '"]').first().find('.card-title').first().text().trim();
                $('#taskDetailModalTitle').text(taskTitle ? taskTitle : 'Task Details');

                $.ajax({
                    url: '/team/tasks/' + taskId + '/details',
                    method: 'GET',
                    success: function(response) {
                        $('#taskDetailContent').html(response);
                        const loadedTitle = $('#taskDetailContent').find('[data-task-title]').first().data('task-title');
                        if (loadedTitle) {
                            $('#taskDetailModalTitle').text(loadedTitle);
                        }
                        initializeComments(taskId, canInteract);
                    },
                    error: function() {
                        $('#taskDetailContent').html('<div class="alert alert-danger">Failed to load task details</div>');
                    }
                });
            }

            function initializeComments(taskId, canInteract = true) {
                if (!canInteract) {
                    $('#task-status-select').prop('disabled', true);
                    $('#new-comment-input').prop('disabled', true).attr('placeholder', 'You can only comment on tasks assigned to you');
                    $('#submit-comment').prop('disabled', true);
                    $('.reply-btn, .submit-reply').prop('disabled', true).addClass('disabled');
                }

                $(document).off('click', '#submit-comment').on('click', '#submit-comment', function() {
                    if (!canInteract) {
                        toastr.error('You can only comment on tasks assigned to you.');
                        return;
                    }

                    const comment = $('#new-comment-input').val().trim();

                    if (!comment) {
                        alert('Please write a comment');
                        return;
                    }

                    $.ajax({
                        url: '/team/tasks/' + taskId + '/comments',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            comment: comment
                        },
                        success: function(response) {
                            $('#new-comment-input').val('');
                            $('#no-comments-msg').remove();
                            $('#comments-list').prepend(response.html);
                            updateCommentCount();
                            toastr.success('Comment posted successfully');
                        },
                        error: function() {
                            toastr.error('Failed to post comment');
                        }
                    });
                });

                $(document).off('click', '.reply-btn').on('click', '.reply-btn', function(e) {
                    e.preventDefault();
                    const commentId = $(this).data('comment-id');
                    $('.reply-form').hide();
                    $('#reply-form-' + commentId).show();
                    $('#reply-form-' + commentId + ' .reply-input').focus();
                });

                $(document).off('click', '.cancel-reply').on('click', '.cancel-reply', function() {
                    $(this).closest('.reply-form').hide();
                    $(this).closest('.reply-form').find('.reply-input').val('');
                });

                $(document).off('click', '.submit-reply').on('click', '.submit-reply', function() {
                    if (!canInteract) {
                        toastr.error('You can only reply on tasks assigned to you.');
                        return;
                    }

                    const parentId = $(this).data('parent-id');
                    const reply = $(this).closest('.reply-form').find('.reply-input').val().trim();

                    if (!reply) {
                        alert('Please write a reply');
                        return;
                    }

                    $.ajax({
                        url: '/team/tasks/' + taskId + '/comments',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            comment: reply,
                            parent_id: parentId
                        },
                        success: function(response) {
                            const $replyForm = $('#reply-form-' + parentId);
                            $replyForm.hide();
                            $replyForm.find('.reply-input').val('');

                            let $repliesContainer = $('[data-comment-id="' + parentId + '"]').find('.replies').first();
                            if ($repliesContainer.length === 0) {
                                $repliesContainer = $('<div class="replies mt-3 ps-3" style="padding-left: 20px; border-left: 2px solid #dee2e6;"></div>');
                                $replyForm.before($repliesContainer);
                            }

                            $repliesContainer.append(response.html);
                            updateCommentCount();
                            toastr.success('Reply posted successfully');
                        },
                        error: function() {
                            toastr.error('Failed to post reply');
                        }
                    });
                });

                $(document).off('change', '#task-status-select').on('change', '#task-status-select', function() {
                    if (!canInteract) {
                        toastr.error('You can only change status for tasks assigned to you.');
                        return;
                    }

                    const taskId = $(this).data('task-id');
                    const newStatus = $(this).val();

                    $.ajax({
                        url: '/team/tasks/' + taskId + '/update-status',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function() {
                            const $taskCard = $('.task-card[data-task-id="' + taskId + '"]');
                            const $targetColumn = $('.task-column[data-status="' + newStatus + '"]');

                            if ($taskCard.length && $targetColumn.length) {
                                $targetColumn.append($taskCard);
                                updateBadgeCounts();
                            }

                            toastr.success('Task status updated');
                        },
                        error: function(xhr) {
                            const message = xhr?.responseJSON?.message || 'Failed to update task status';
                            toastr.error(message);
                            showTaskDetails(taskId);
                        }
                    });
                });
            }

            function updateCommentCount() {
                const count = $('#comments-list .comment-item').length;
                $('.comments-section h5 .badge').text(count);
            }

            function updateBadgeCounts() {
                $('.task-column').each(function() {
                    const count = $(this).find('.task-card').length;
                    $(this).closest('.card').find('.badge').text(count);
                });
            }

            const openTaskIdFromUrl = new URLSearchParams(window.location.search).get('open_task');
            if (openTaskIdFromUrl) {
                const canInteract = String($('.task-card[data-task-id="' + openTaskIdFromUrl + '"]').first().data('can-move')) === '1';
                showTaskDetails(openTaskIdFromUrl, canInteract);

                if (window.history && typeof window.history.replaceState === 'function') {
                    const cleanedUrl = window.location.pathname + window.location.hash;
                    window.history.replaceState({}, document.title, cleanedUrl);
                }
            }

            if (typeof toastr === 'undefined') {
                window.toastr = {
                    success: function(msg) { alert('Success: ' + msg); },
                    error: function(msg) { alert('Error: ' + msg); }
                };
            }
        });
    </script>
@stop
