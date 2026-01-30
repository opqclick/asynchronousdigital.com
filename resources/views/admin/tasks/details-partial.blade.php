<div class="row">
    <div class="col-md-6">
        <h6><strong>Project:</strong></h6>
        <p>{{ $task->project->name }}</p>
    </div>
    <div class="col-md-6">
        <h6><strong>Status:</strong></h6>
        <select class="form-control" id="task-status-select" data-task-id="{{ $task->id }}">
            <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h6><strong>Priority:</strong></h6>
        <p>
            <span class="badge badge-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'normal' ? 'info' : 'secondary')) }}">
                {{ ucfirst($task->priority) }}
            </span>
        </p>
    </div>
    <div class="col-md-6">
        <h6><strong>Due Date:</strong></h6>
        <p>{{ $task->due_date ? $task->due_date->format('F d, Y') : 'Not set' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h6><strong>Estimated Hours:</strong></h6>
        <p>{{ $task->estimated_hours ?? 'Not set' }}</p>
    </div>
    <div class="col-md-6">
        <h6><strong>Actual Hours:</strong></h6>
        <p>{{ $task->actual_hours ?? 'Not tracked' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h6><strong>Assigned To:</strong></h6>
        <p>
            @forelse($task->users as $user)
                <span class="badge badge-info mr-1">{{ $user->name }}</span>
            @empty
                <span class="text-muted">No users assigned</span>
            @endforelse
        </p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h6><strong>Description:</strong></h6>
        <p>{!! nl2br(e($task->description ?? 'No description provided')) !!}</p>
    </div>
</div>

@if($task->attachments && count($task->attachments) > 0)
    <div class="row">
        <div class="col-12">
            <h6><strong>Attachments:</strong></h6>
            <ul class="list-unstyled">
                @foreach($task->attachments as $attachment)
                    <li>
                        <a href="{{ Storage::url($attachment) }}" target="_blank">
                            <i class="fas fa-file"></i> {{ basename($attachment) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row mt-3">
    <div class="col-6">
        <small class="text-muted">Created: {{ $task->created_at->format('M d, Y h:i A') }}</small>
    </div>
    <div class="col-6 text-right">
        <small class="text-muted">Updated: {{ $task->updated_at->format('M d, Y h:i A') }}</small>
    </div>
</div>

<hr class="my-4">

<!-- Comments Section -->
<div class="comments-section">
    <h5 class="mb-3">
        <i class="fas fa-comments"></i> Comments 
        <span class="badge badge-secondary">{{ $task->comments->count() }}</span>
    </h5>

    <!-- Add Comment Form -->
    <div class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" id="new-comment-input" placeholder="Write a comment...">
            <div class="input-group-append">
                <button class="btn btn-primary" id="submit-comment" data-task-id="{{ $task->id }}">
                    <i class="fas fa-paper-plane"></i> Post
                </button>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div id="comments-list">
        @forelse($task->comments as $comment)
            @include('admin.tasks.comment-item', ['comment' => $comment])
        @empty
            <div class="text-center text-muted py-4" id="no-comments-msg">
                <i class="fas fa-comment-slash fa-2x mb-2"></i>
                <p>No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>
</div>
