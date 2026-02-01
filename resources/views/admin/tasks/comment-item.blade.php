<div class="comment-item mb-3" data-comment-id="{{ $comment->id }}">
    <div class="d-flex">
        <div class="flex-shrink-0">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 40px; height: 40px; font-size: 18px;">
                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
            </div>
        </div>
        <div class="flex-grow-1 ms-3" style="margin-left: 15px;">
            <div class="bg-light p-3 rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">{{ $comment->user->name }}</h6>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0">{{ $comment->comment }}</p>
            </div>
            <div class="mt-2">
                <a href="#" class="text-primary small reply-btn" data-comment-id="{{ $comment->id }}">
                    <i class="fas fa-reply"></i> Reply
                </a>
            </div>

            <!-- Reply Form (Hidden by default) -->
            <div class="reply-form mt-2" id="reply-form-{{ $comment->id }}" style="display: none;">
                <div class="input-group">
                    <input type="text" class="form-control reply-input" placeholder="Write a reply...">
                    <div class="input-group-append">
                        <button class="btn btn-primary submit-reply" data-parent-id="{{ $comment->id }}">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <button class="btn btn-secondary cancel-reply">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            @if($comment->replies && $comment->replies->count() > 0)
                <div class="replies mt-3 ps-3" style="padding-left: 20px; border-left: 2px solid #dee2e6;">
                    @foreach($comment->replies as $reply)
                        @include('admin.tasks.comment-item', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
