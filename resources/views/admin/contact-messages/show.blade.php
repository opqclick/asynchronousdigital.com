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
                    <h3 class="card-title">{{ $contactMessage->subject }}</h3>
                    <div class="card-tools">
                        @if($contactMessage->status == 'new')
                            <span class="badge badge-danger">New</span>
                        @elseif($contactMessage->status == 'read')
                            <span class="badge badge-primary">Read</span>
                        @elseif($contactMessage->status == 'replied')
                            <span class="badge badge-success">Replied</span>
                        @else
                            <span class="badge badge-secondary">Archived</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">From:</dt>
                        <dd class="col-sm-9">{{ $contactMessage->name }}</dd>

                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9">
                            <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a>
                        </dd>

                        @if($contactMessage->phone)
                        <dt class="col-sm-3">Phone:</dt>
                        <dd class="col-sm-9">
                            <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a>
                        </dd>
                        @endif

                        @if($contactMessage->company)
                        <dt class="col-sm-3">Company:</dt>
                        <dd class="col-sm-9">{{ $contactMessage->company }}</dd>
                        @endif

                        @if($contactMessage->service_interest)
                        <dt class="col-sm-3">Service Interest:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $contactMessage->service_interest }}</span>
                        </dd>
                        @endif

                        @if($contactMessage->budget_range)
                        <dt class="col-sm-3">Budget Range:</dt>
                        <dd class="col-sm-9">{{ $contactMessage->budget_range }}</dd>
                        @endif

                        <dt class="col-sm-3">Received:</dt>
                        <dd class="col-sm-9">{{ $contactMessage->created_at->format('M d, Y h:i A') }}</dd>

                        @if($contactMessage->read_at)
                        <dt class="col-sm-3">Read:</dt>
                        <dd class="col-sm-9">{{ $contactMessage->read_at->format('M d, Y h:i A') }}</dd>
                        @endif
                    </dl>

                    <hr>

                    <h5>Message:</h5>
                    <div class="p-3 bg-light rounded">
                        {{ $contactMessage->message }}
                    </div>

                    @if($contactMessage->internal_notes)
                    <hr>
                    <h5>Internal Notes:</h5>
                    <div class="p-3 bg-warning rounded">
                        {{ $contactMessage->internal_notes }}
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inbox
                    </a>
                    <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" style="display:inline;" 
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
                <form action="{{ route('admin.contact-messages.update', $contactMessage) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="new" {{ $contactMessage->status == 'new' ? 'selected' : '' }}>New</option>
                                <option value="read" {{ $contactMessage->status == 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $contactMessage->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $contactMessage->status == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="assigned_to">Assign To</label>
                            <select name="assigned_to" id="assigned_to" class="form-control">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $contactMessage->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="internal_notes">Internal Notes</label>
                            <textarea name="internal_notes" id="internal_notes" class="form-control" rows="4" 
                                      placeholder="Add notes for team members...">{{ $contactMessage->internal_notes }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Update Message
                        </button>
                    </div>
                </form>
            </div>

            @if($contactMessage->assignedUser)
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Assigned To</h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ $contactMessage->assignedUser->name }}</strong></p>
                    <p class="text-muted">{{ $contactMessage->assignedUser->email }}</p>
                    @if($contactMessage->assignedUser->phone)
                        <p><i class="fas fa-phone"></i> {{ $contactMessage->assignedUser->phone }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@stop
