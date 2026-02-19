@extends('adminlte::page')

@section('title', 'Team Content Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Team Content Details</h1>
        <a href="{{ route('admin.team-contents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $teamContent->name }}</h3>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $teamContent->name }}</dd>

                <dt class="col-sm-3">Role / Designation</dt>
                <dd class="col-sm-9">{{ $teamContent->role_title ?: 'Team Member' }}</dd>

                <dt class="col-sm-3">Bio</dt>
                <dd class="col-sm-9">{{ $teamContent->bio ?: 'N/A' }}</dd>

                <dt class="col-sm-3">Image URL</dt>
                <dd class="col-sm-9">
                    @if($teamContent->image_url)
                        <a href="{{ $teamContent->image_url }}" target="_blank">{{ $teamContent->image_url }}</a>
                    @else
                        N/A
                    @endif
                </dd>

                <dt class="col-sm-3">Display Order</dt>
                <dd class="col-sm-9">{{ $teamContent->display_order }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-{{ $teamContent->is_published ? 'success' : 'secondary' }}">
                        {{ $teamContent->is_published ? 'Published' : 'Draft' }}
                    </span>
                </dd>
            </dl>
        </div>
    </div>
@stop
