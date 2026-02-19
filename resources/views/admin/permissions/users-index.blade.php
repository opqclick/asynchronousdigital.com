@extends('adminlte::page')

@section('title', 'User Permission Overrides')

@section('content_header')
    <h1>User Permission Overrides</h1>
@stop

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card title="Manage User Overrides" theme="primary" icon="fas fa-user-cog">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleNames = $user->roles->pluck('display_name')->all();
                                @endphp
                                {{ implode(', ', $roleNames) ?: optional($user->role)->display_name }}
                            </td>
                            <td>
                                <a href="{{ route('admin.permissions.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Edit Overrides
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-adminlte-card>
@stop
