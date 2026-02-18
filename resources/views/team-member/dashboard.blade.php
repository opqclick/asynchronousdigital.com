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
                        <a href="{{ route('team-member.tasks.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Task
                        </a>
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
                                        <div class="card mb-2 task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
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
                                        <div class="card mb-2 border-primary task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
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
                                        <div class="card mb-2 border-warning task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
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
                                        <div class="card mb-2 border-success task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
                                            <div class="card-body p-2">
                                                <h6 class="card-title">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted">{{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-success">
                                                        Completed
                                                    </span>
                                                    @if($task->updated_at)
                                                        <small class="text-muted">{{ $task->updated_at->format('M d') }}</small>
                                                    @endif
                                                </div>
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

    <!-- Task Detail Modal -->
    <div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Details</h5>
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
        .task-column.drag-over {
            background-color: #e9ecef;
            border: 2px dashed #007bff;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let draggedTask = null;

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
                    const taskId = $(this).data('task-id');
                    showTaskDetails(taskId);
                }
            });

            function showTaskDetails(taskId) {
                $('#taskDetailModal').modal('show');

                $.ajax({
                    url: '/team/tasks/' + taskId + '/details',
                    method: 'GET',
                    success: function(response) {
                        $('#taskDetailContent').html(response);
                        initializeComments(taskId);
                    },
                    error: function() {
                        $('#taskDetailContent').html('<div class="alert alert-danger">Failed to load task details</div>');
                    }
                });
            }

            function initializeComments(taskId) {
                $(document).off('click', '#submit-comment').on('click', '#submit-comment', function() {
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

            if (typeof toastr === 'undefined') {
                window.toastr = {
                    success: function(msg) { alert('Success: ' + msg); },
                    error: function(msg) { alert('Error: ' + msg); }
                };
            }
        });
    </script>
@stop
