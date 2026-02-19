@extends('adminlte::page')

@section('title', 'Edit User Permission Overrides')

@section('content_header')
    <h1>Edit Overrides: {{ $user->name }}</h1>
@stop

@section('content')
    <x-adminlte-card title="Role vs User Permission Overrides" theme="primary" icon="fas fa-user-shield">
        @if ($errors->any())
            <x-adminlte-alert theme="danger" title="Validation Error" dismissable>
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-adminlte-alert>
        @endif

        <p class="text-muted">
            Role permissions are the baseline. User overrides have priority.
            <strong>Denied</strong> blocks access and <strong>Allowed</strong> grants access for this user.
        </p>

        <form method="POST" action="{{ route('admin.permissions.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th width="180">Role Baseline</th>
                            <th width="160">Allow</th>
                            <th width="160">Deny</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    @if (in_array($permission->id, $rolePermissionIds))
                                        <span class="badge badge-success">Granted</span>
                                    @else
                                        <span class="badge badge-secondary">Not Granted</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input permission-allow"
                                            id="allow_{{ $permission->id }}"
                                            name="allow_permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('allow_permissions', $allowedPermissionIds)) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="allow_{{ $permission->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input permission-deny"
                                            id="deny_{{ $permission->id }}"
                                            name="deny_permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('deny_permissions', $deniedPermissionIds)) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="deny_{{ $permission->id }}"></label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
                <a href="{{ route('admin.permissions.users.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </x-adminlte-card>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input.permission-allow').forEach(function (allowCheckbox) {
            allowCheckbox.addEventListener('change', function () {
                if (!this.checked) return;
                const permissionId = this.id.replace('allow_', '');
                const denyCheckbox = document.getElementById('deny_' + permissionId);
                if (denyCheckbox) denyCheckbox.checked = false;
            });
        });

        document.querySelectorAll('input.permission-deny').forEach(function (denyCheckbox) {
            denyCheckbox.addEventListener('change', function () {
                if (!this.checked) return;
                const permissionId = this.id.replace('deny_', '');
                const allowCheckbox = document.getElementById('allow_' + permissionId);
                if (allowCheckbox) allowCheckbox.checked = false;
            });
        });
    });
</script>
@endpush
