@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Notifications</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-sm btn-outline-primary" type="submit">Mark all as read</button>
            </form>
        @endif
    </div>
@stop

@section('content')
    <x-adminlte-card theme="primary" icon="fas fa-bell" title="All Notifications">
        @if($notifications->count() === 0)
            <p class="text-muted mb-0">No notifications found.</p>
        @else
            <div class="list-group">
                @foreach($notifications as $notification)
                    @php
                        $isRead = !is_null($notification->read_at);
                        $payload = $notification->data;
                    @endphp
                    <div class="list-group-item {{ $isRead ? '' : 'list-group-item-info' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="font-weight-bold">{{ $payload['message'] ?? 'Notification received' }}</div>
                                @if(!empty($payload['project_name']))
                                    <small class="text-muted d-block">Project: {{ $payload['project_name'] }}</small>
                                @endif
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @if(!$isRead)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-xs btn-primary" type="submit">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-adminlte-card>
@stop
