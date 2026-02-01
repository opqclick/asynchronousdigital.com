@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Admin Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['active_projects'] }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="small-box-footer">
                    View Projects <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_clients'] }}</h3>
                    <p>Active Clients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.clients.index') }}" class="small-box-footer">
                    View Clients <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_tasks'] }}</h3>
                    <p>Pending Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('admin.tasks.index') }}" class="small-box-footer">
                    View Tasks <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($stats['unpaid_invoices'], 2) }}</h3>
                    <p>Unpaid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.invoices.index') }}" class="small-box-footer">
                    View Invoices <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Task Board - Trello Style -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Board</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Task
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
                                                <h6 class="card-title mb-1">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted mb-2"><i class="fas fa-folder"></i> {{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted"><i class="far fa-calendar"></i> {{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $user)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1" 
                                                                     style="width: 24px; height: 24px; font-size: 10px;" 
                                                                     title="{{ $user->name }}">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
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
                                        <div class="card mb-2 border-primary task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-1">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted mb-2"><i class="fas fa-folder"></i> {{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted"><i class="far fa-calendar"></i> {{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $user)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1" 
                                                                     style="width: 24px; height: 24px; font-size: 10px;" 
                                                                     title="{{ $user->name }}">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
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
                                        <div class="card mb-2 border-warning task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-1">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted mb-2"><i class="fas fa-folder"></i> {{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted"><i class="far fa-calendar"></i> {{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $user)
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-1" 
                                                                     style="width: 24px; height: 24px; font-size: 10px;" 
                                                                     title="{{ $user->name }}">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
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
                                        <div class="card mb-2 border-success task-card" draggable="true" data-task-id="{{ $task->id }}" style="cursor: move;">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-1">{{ $task->title }}</h6>
                                                <p class="card-text small text-muted mb-2"><i class="fas fa-folder"></i> {{ $task->project->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge badge-success">
                                                        Completed
                                                    </span>
                                                    @if($task->due_date)
                                                        <small class="text-muted"><i class="far fa-calendar"></i> {{ $task->due_date->format('M d') }}</small>
                                                    @endif
                                                </div>
                                                @if($task->users->count() > 0)
                                                    <div class="d-flex align-items-center" style="font-size: 0.75rem;">
                                                        <span class="text-muted mr-1">Assignees:</span>
                                                        <div class="d-flex">
                                                            @foreach($task->users->take(3) as $user)
                                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-1" 
                                                                     style="width: 24px; height: 24px; font-size: 10px;" 
                                                                     title="{{ $user->name }}">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
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
                    <a href="#" id="editTaskBtn" class="btn btn-primary" target="_blank">
                        <i class="fas fa-edit"></i> Edit Task
                    </a>
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
        .card-body {
            overflow-y: auto;
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
        console.log('Admin Dashboard loaded');

        $(document).ready(function() {
            let draggedTask = null;

            // Make task cards draggable
            $('.task-card').on('dragstart', function(e) {
                draggedTask = $(this);
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/html', $(this).html());
            });

            $('.task-card').on('dragend', function(e) {
                $(this).removeClass('dragging');
            });

            // Handle drop zones
            $('.task-column').on('dragover', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                $(this).addClass('drag-over');
                return false;
            });

            $('.task-column').on('dragleave', function(e) {
                $(this).removeClass('drag-over');
            });

            $('.task-column').on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                $(this).removeClass('drag-over');
                
                if (draggedTask) {
                    const taskId = draggedTask.data('task-id');
                    const newStatus = $(this).data('status');
                    const oldStatus = draggedTask.closest('.task-column').data('status');
                    const $originalColumn = draggedTask.closest('.task-column');
                    const taskTitle = draggedTask.find('.card-title').text();
                    
                    // Format status names for display
                    const statusNames = {
                        'to_do': 'To Do',
                        'in_progress': 'In Progress',
                        'review': 'Review',
                        'done': 'Done'
                    };
                    
                    // Only confirm if status is actually changing
                    if (oldStatus !== newStatus) {
                        const confirmMessage = 'Change "' + taskTitle + '" status from "' + statusNames[oldStatus] + '" to "' + statusNames[newStatus] + '"?';
                        
                        if (!confirm(confirmMessage)) {
                            // User cancelled, don't move the card
                            draggedTask = null;
                            return false;
                        }
                    }
                    
                    // Move the card visually
                    $(this).append(draggedTask);
                    
                    // Update via AJAX
                    $.ajax({
                        url: '/admin/tasks/' + taskId + '/update-status',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function(response) {
                            console.log('Task status updated successfully', response);
                            
                            // Update badge counts
                            updateBadgeCounts();
                        },
                        error: function(xhr) {
                            // Revert on error
                            toastr.error('Failed to update task status');
                            location.reload();
                        }
                    });
                    
                    draggedTask = null;
                }
                
                return false;
            });

            // Click on task card to show details
            $(document).on('click', '.task-card', function(e) {
                if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                    const taskId = $(this).data('task-id');
                    showTaskDetails(taskId);
                }
            });

            function showTaskDetails(taskId) {
                $('#taskDetailModal').modal('show');
                $('#editTaskBtn').attr('href', '/admin/tasks/' + taskId + '/edit');
                
                $.ajax({
                    url: '/admin/tasks/' + taskId + '/details',
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
                // Submit new comment
                $(document).off('click', '#submit-comment').on('click', '#submit-comment', function() {
                    const comment = $('#new-comment-input').val().trim();
                    
                    if (!comment) {
                        alert('Please write a comment');
                        return;
                    }
                    
                    $.ajax({
                        url: '/admin/tasks/' + taskId + '/comments',
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

                // Show reply form
                $(document).off('click', '.reply-btn').on('click', '.reply-btn', function(e) {
                    e.preventDefault();
                    const commentId = $(this).data('comment-id');
                    $('.reply-form').hide();
                    $('#reply-form-' + commentId).show();
                    $('#reply-form-' + commentId + ' .reply-input').focus();
                });

                // Cancel reply
                $(document).off('click', '.cancel-reply').on('click', '.cancel-reply', function() {
                    $(this).closest('.reply-form').hide();
                    $(this).closest('.reply-form').find('.reply-input').val('');
                });

                // Submit reply
                $(document).off('click', '.submit-reply').on('click', '.submit-reply', function() {
                    const parentId = $(this).data('parent-id');
                    const reply = $(this).closest('.reply-form').find('.reply-input').val().trim();
                    
                    if (!reply) {
                        alert('Please write a reply');
                        return;
                    }
                    
                    $.ajax({
                        url: '/admin/tasks/' + taskId + '/comments',
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
                            
                            // Find or create replies container
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

                // Enter key to submit
                $('#new-comment-input').off('keypress').on('keypress', function(e) {
                    if (e.which === 13) {
                        $('#submit-comment').click();
                    }
                });

                $('.reply-input').off('keypress').on('keypress', function(e) {
                    if (e.which === 13) {
                        $(this).closest('.reply-form').find('.submit-reply').click();
                    }
                });

                // Status change from modal
                $(document).off('change', '#task-status-select').on('change', '#task-status-select', function() {
                    const taskId = $(this).data('task-id');
                    const newStatus = $(this).val();
                    
                    $.ajax({
                        url: '/admin/tasks/' + taskId + '/update-status',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function(response) {
                            console.log('Task status updated from modal', response);
                            
                            // Find and move the task card to the appropriate column
                            const $taskCard = $('.task-card[data-task-id="' + taskId + '"]');
                            const $targetColumn = $('.task-column[data-status="' + newStatus + '"]');
                            
                            if ($taskCard.length && $targetColumn.length) {
                                $targetColumn.append($taskCard);
                                updateBadgeCounts();
                            }
                            
                            toastr.success('Task status updated');
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update task status');
                            // Reload modal to revert select
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
                    const status = $(this).data('status');
                    const count = $(this).find('.task-card').length;
                    $(this).closest('.card').find('.badge').text(count);
                });
            }

            // Initialize toastr if not already available
            if (typeof toastr === 'undefined') {
                window.toastr = {
                    success: function(msg) { 
                        alert('Success: ' + msg); 
                    },
                    error: function(msg) { 
                        alert('Error: ' + msg); 
                    }
                };
            }
        });
    </script>
@stop
