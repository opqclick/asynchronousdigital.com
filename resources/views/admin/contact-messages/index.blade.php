@extends('adminlte::page')

@section('title', 'Contact Messages')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    Contact Messages Inbox
                    @if($unreadCount > 0)
                        <span class="badge badge-danger">{{ $unreadCount }} unread</span>
                    @endif
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Contact Messages</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Messages</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $newCount }} new</span>
                <span class="badge badge-warning">{{ $unreadCount }} unread</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mailbox-messages">
                <table id="messages-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Subject</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="{{ !$message->read_at ? 'font-weight-bold' : '' }} {{ $message->trashed() ? 'table-secondary' : '' }}">
                                <td>
                                    @if($message->status == 'new')
                                        <span class="badge badge-danger">New</span>
                                    @elseif($message->status == 'read')
                                        <span class="badge badge-primary">Read</span>
                                    @elseif($message->status == 'replied')
                                        <span class="badge badge-success">Replied</span>
                                    @else
                                        <span class="badge badge-secondary">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$message->read_at)
                                        <i class="fas fa-circle text-danger" style="font-size: 8px;"></i>
                                    @endif
                                    {{ $message->name }}
                                    @if($message->trashed() && auth()->user()->isAdmin())
                                        <span class="badge badge-danger ml-1">Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->company ?? 'N/A' }}</td>
                                <td>{{ Str::limit($message->subject, 40) }}</td>
                                <td>{{ $message->service_interest ?? 'N/A' }}</td>
                                <td>{{ $message->created_at->diffForHumans() }}</td>
                                <td>
                                    @if($message->trashed())
                                        <form action="{{ route('admin.recycle-bin.restore', ['type' => 'contact-messages', 'id' => $message->id]) }}" method="POST" style="display:inline;" data-confirm-message="Restore this message?">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @elseif(!$message->trashed())
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" style="display:inline;" 
                                              data-confirm-message="Are you sure?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#messages-table').DataTable({
            order: [[6, 'desc']],
            columnDefs: [
                { orderable: false, targets: 7 }
            ]
        });
    });
</script>
@stop

@section('css')
<style>
    .font-weight-bold {
        font-weight: 600;
    }
</style>
@stop
