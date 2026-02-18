@extends('adminlte::page')

@section('title', 'Role Permissions')

@section('content_header')
    <h1>Role Permissions</h1>
@stop

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card title="Manage Role Permissions" theme="primary" icon="fas fa-user-shield">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->display_name }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Edit Permissions
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-adminlte-card>
@stop
