@extends('adminlte::page')

@section('title', 'Edit Role Permissions')

@section('content_header')
    <h1>Edit Permissions: {{ $role->display_name }}</h1>
@stop

@section('content')
    <x-adminlte-card title="Role Permission Matrix" theme="primary" icon="fas fa-key">
        <form method="POST" action="{{ route('admin.permissions.roles.update', $role) }}">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach ($permissions as $permission)
                    <div class="col-md-4 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="permission_{{ $permission->id }}"
                                name="permission_ids[]"
                                value="{{ $permission->id }}"
                                {{ in_array($permission->id, old('permission_ids', $assignedPermissionIds)) ? 'checked' : '' }}
                            >
                            <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('permission_ids')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
                <a href="{{ route('admin.permissions.roles.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </x-adminlte-card>
@stop
