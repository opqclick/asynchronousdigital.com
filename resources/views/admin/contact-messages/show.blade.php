@extends('adminlte::page')

@section('title', 'Contact Message')

@section('content_header')
    <h1>Contact Message Details</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $message->subject }}</h3>
                    <div class="card-tools">
                        @if($message->status == 'new')
                            <span class="badge badge-danger">New</span>
                        @elseif($message->status == 'read')
                            <span class="badge badge-primary">Read</span>
                        @elseif($message->status == 'replied')
                            <span class="badge badge-success">Replied</span>
                        @else
                            <span class="badge badge-secondary">Archived</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">From:</dt>
                        <dd class="col-sm-9">{{ $message->name }}</dd>

                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9">
                            <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                        </dd>

                        @if($message->phone)
                        <dt class="col-sm-3">Phone:</dt>
                        <dd class="col-sm-9">
                            <a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>
                        </dd>
                        @endif

                        @if($message->company)
                        <dt class="col-sm-3">Company:</dt>
                        <dd class="col-sm-9">{{ $message->company }}</dd>
                        @endif

                        @if($message->service_interest)
                        <dt class="col-sm-3">Service Interest:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $message->service_interest }}</span>
                        </dd>
                        @endif

                        @if($message->budget_range)
                        <dt class="col-sm-3">Budget Range:</dt>
                        <dd class="col-sm-9">{{ $message->budget_range }}</dd>
                        @endif

                        <dt class="col-sm-3">Received:</dt>
                        <dd class="col-sm-9">{{ $message->created_at->format('M d, Y h:i A') }}</dd>

                        @if($message->read_at)
                        <dt class="col-sm-3">Read:</dt>
                        <dd class="col-sm-9">{{ $message->read_at->format('M d, Y h:i A') }}</dd>
                        @endif
                    </dl>

                    <hr>

                    <h5>Message:</h5>
                    <div class="p-3 bg-light rounded">
                        {{ $message->message }}
                    </div>

                    @if($message->internal_notes)
                    <hr>
                    <h5>Internal Notes:</h5>
                    <div class="p-3 bg-warning rounded">
                        {{ $message->internal_notes }}
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inbox
                    </a>
                    <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Are you sure you want to delete this message?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Message Management</h3>
                </div>
                <form action="{{ route('admin.contact-messages.update', $message) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="new" {{ $message->status == 'new' ? 'selected' : '' }}>New</option>
                                <option value="read" {{ $message->status == 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $message->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $message->status == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="assigned_to">Assign To</label>
                            <select name="assigned_to" id="assigned_to" class="form-control">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $message->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="internal_notes">Internal Notes</label>
                            <textarea name="internal_notes" id="internal_notes" class="form-control" rows="4" 
                                      placeholder="Add notes for team members...">{{ $message->internal_notes }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Update Message
                        </button>
                    </div>
                </form>
            </div>

            @if($message->assignedUser)
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Assigned To</h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ $message->assignedUser->name }}</strong></p>
                    <p class="text-muted">{{ $message->assignedUser->email }}</p>
                    @if($message->assignedUser->phone)
                        <p><i class="fas fa-phone"></i> {{ $message->assignedUser->phone }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@stop
