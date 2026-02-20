@extends('adminlte::page')

@section('title', 'Users Management')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Users Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Users</h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="usersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Teams</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="{{ $user->trashed() ? 'table-secondary' : '' }}">
                        <td>{{ $user->id }}</td>
                        <td>
                            {{ $user->name }}
                            @if($user->trashed())
                                <span class="badge badge-danger ml-1">Deleted</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span
                                    class="badge badge-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'project_manager' ? 'warning' : ($role->name === 'team_member' ? 'primary' : 'info')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}{{ ($user->role && $user->role->id === $role->id) ? ' (Active)' : '' }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->teams->count() > 0)
                                @foreach($user->teams as $team)
                                    <span class="badge badge-secondary">{{ $team->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No teams</span>
                            @endif
                        </td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($user->trashed() && auth()->user()->isAdmin())
                                    <form
                                        action="{{ route('admin.recycle-bin.restore', ['type' => 'users', 'id' => $user->id]) }}"
                                        method="POST" style="display:inline;"
                                        data-confirm-message="Restore this user and related deleted records?">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @elseif(!$user->trashed())
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.send-invitation', $user) }}" method="POST"
                                        style="display:inline;"
                                        data-confirm-message="Resend invitation email to {{ $user->email }}?">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Resend Invitation Email">
                                            <i class="fas fa-paper-plane mr-1"></i> Invite
                                        </button>
                                    </form>
                                    @if(auth()->user()->isAdmin() && auth()->id() !== $user->id && !$user->hasAssignedRole('admin') && !session()->has('impersonator_id'))
                                        <form action="{{ route('admin.users.impersonate', $user) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm"
                                                title="Login As {{ $user->name }}">
                                                <i class="fas fa-user-secret mr-1"></i> Login As
                                            </button>
                                        </form>
                                        {{-- Custom Email button --}}
                                        <button type="button" class="btn btn-primary btn-sm" title="Send Custom Email"
                                            data-toggle="modal" data-target="#sendEmailModal" data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-url="{{ route('admin.users.send-email', $user) }}">
                                            <i class="fas fa-envelope mr-1"></i> Email
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        style="display:inline;"
                                        data-confirm-message="Are you sure you want to delete this user?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- Send Custom Email Modal --}}
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendEmailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">To: <strong id="modal-user-email"></strong></p>
                <div class="form-group">
                    <label for="email-subject">Subject <span class="text-danger">*</span></label>
                    <input type="text" id="email-subject" class="form-control" placeholder="Enter email subject"
                        maxlength="255">
                </div>
                <div class="form-group mb-0">
                    <label for="email-body">Message <span class="text-danger">*</span></label>
                    <textarea id="email-body" class="form-control" rows="8"
                        placeholder="Enter your message..."></textarea>
                </div>
                <div id="send-email-result" class="mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="send-email-submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-1"></i> Send Email
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "responsive": true,
            "order": [[0, "desc"]]
        });

        // Populate modal when opened
        var currentSendUrl = '';
        $('#sendEmailModal').on('show.bs.modal', function (event) {
            var btn = $(event.relatedTarget);
            currentSendUrl = btn.data('url');
            $('#modal-user-email').text(btn.data('user-email'));
            $('#sendEmailModalLabel').text('Email to ' + btn.data('user-name'));
            $('#email-subject').val('');
            $('#email-body').val('');
            $('#send-email-result').hide().html('');
            $('#send-email-submit').prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Email');
        });

        // AJAX send
        $('#send-email-submit').on('click', function () {
            var subject = $('#email-subject').val().trim();
            var body = $('#email-body').val().trim();
            var resultBox = $('#send-email-result');

            if (!subject || !body) {
                resultBox.show().html('<div class="alert alert-warning mb-0 py-2">Please fill in both subject and message.</div>');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
            resultBox.hide().html('');

            $.ajax({
                url: currentSendUrl,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    subject: subject,
                    body: body,
                },
                success: function (data) {
                    resultBox.show().html('<div class="alert alert-success mb-0 py-2"><i class="fas fa-check-circle mr-1"></i>' + data.message + '</div>');
                    $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Email');
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred.';
                    resultBox.show().html('<div class="alert alert-danger mb-0 py-2"><i class="fas fa-times-circle mr-1"></i>' + msg + '</div>');
                    $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Email');
                }
            });
        });
    });
</script>
@stop