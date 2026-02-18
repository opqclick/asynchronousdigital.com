@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('adminlte::page')

@section('title', 'Task Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Task Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
                    <li class="breadcrumb-item active">{{ $task->title }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $task->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.tasks.details-partial', ['task' => $task])
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Task Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Project:</dt>
                        <dd class="col-sm-7">
                            <a href="{{ route('admin.projects.show', $task->project) }}">
                                {{ $task->project->name }}
                            </a>
                        </dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
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
                        </dd>

                        <dt class="col-sm-5">Priority:</dt>
                        <dd class="col-sm-7">
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
                        </dd>

                        @if($task->estimated_hours)
                            <dt class="col-sm-5">Estimated:</dt>
                            <dd class="col-sm-7">{{ $task->estimated_hours }} hours</dd>
                        @endif

                        @if($task->due_date)
                            <dt class="col-sm-5">Due Date:</dt>
                            <dd class="col-sm-7">
                                @if($task->due_date->isPast() && $task->status !== 'done')
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $task->due_date->format('M d, Y') }}
                                        ({{ $task->due_date->diffForHumans() }})
                                    </span>
                                @else
                                    {{ $task->due_date->format('M d, Y') }}
                                    @if(!$task->due_date->isPast())
                                        ({{ $task->due_date->diffForHumans() }})
                                    @endif
                                @endif
                            </dd>
                        @endif

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">{{ $task->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-5">Updated:</dt>
                        <dd class="col-sm-7">{{ $task->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $task->title }}">
                        <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                        <input type="hidden" name="priority" value="{{ $task->priority }}">
                        
                        <div class="form-group">
                            <label>Change Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="to_do" {{ $task->status === 'to_do' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                                <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                    </form>

                    <hr>

                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Edit Task
                    </a>
                    
                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this task?')">
                            <i class="fas fa-trash"></i> Delete Task
                        </button>
                    </form>
                </div>
            </div>

            <!-- Task Attachments -->
            @if($task->attachments && count($task->attachments) > 0)
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip"></i> Attachments
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($task->attachments as $attachment)
                            <a href="{{ Storage::disk('do_spaces')->url($attachment['path']) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                               target="_blank">
                                <div>
                                    <i class="fas fa-file mr-2"></i>
                                    <strong>{{ $attachment['name'] }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Size: {{ number_format($attachment['size'] / 1024, 2) }} KB
                                        | Uploaded: {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M d, Y') }}
                                    </small>
                                </div>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            const taskId = {{ (int) $task->id }};

            const initializeTaskDetailsActions = function () {
                $(document).off('click', '#submit-comment').on('click', '#submit-comment', function() {
                    const comment = $('#new-comment-input').val().trim();

                    if (!comment) {
                        alert('Please write a comment');
                        return;
                    }

                    $.ajax({
                        url: '{{ route('admin.tasks.comments.store', $task) }}',
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
                        url: '{{ route('admin.tasks.comments.store', $task) }}',
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
                    const newStatus = $(this).val();

                    $.ajax({
                        url: '{{ route('admin.tasks.update-status', $task) }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function() {
                            toastr.success('Task status updated');
                            window.location.reload();
                        },
                        error: function() {
                            toastr.error('Failed to update task status');
                            window.location.reload();
                        }
                    });
                });

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
            };

            const updateCommentCount = function () {
                const count = $('#comments-list .comment-item').length;
                $('.comments-section h5 .badge').text(count);
            };

            if (typeof toastr === 'undefined') {
                window.toastr = {
                    success: function(msg) { alert('Success: ' + msg); },
                    error: function(msg) { alert('Error: ' + msg); },
                };
            }

            initializeTaskDetailsActions(taskId);
        });
    </script>
@stop
