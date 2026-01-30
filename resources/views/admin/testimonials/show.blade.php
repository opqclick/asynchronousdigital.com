@extends('adminlte::page')

@section('title', 'Testimonial Details')

@section('content_header')
    <h1>Testimonial Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        {{ $testimonial->rating }}/5 Stars
                    </h3>
                    <div class="card-tools">
                        @if($testimonial->is_featured)
                            <span class="badge badge-warning">Featured</span>
                        @endif
                        <span class="badge badge-{{ $testimonial->is_published ? 'success' : 'secondary' }}">
                            {{ $testimonial->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote">
                        <p class="mb-0">{{ $testimonial->content }}</p>
                        <footer class="blockquote-footer mt-3">
                            {{ $testimonial->client_name }}
                            @if($testimonial->client_position)
                                , <cite>{{ $testimonial->client_position }}</cite>
                            @endif
                            @if($testimonial->client_company)
                                at <strong>{{ $testimonial->client_company }}</strong>
                            @endif
                        </footer>
                    </blockquote>

                    <hr>

                    <dl class="row">
                        @if($testimonial->client)
                        <dt class="col-sm-3">Linked Client:</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('admin.clients.show', $testimonial->client) }}">
                                {{ $testimonial->client->user->name }} - {{ $testimonial->client->company_name }}
                            </a>
                        </dd>
                        @endif

                        @if($testimonial->project)
                        <dt class="col-sm-3">Linked Project:</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('admin.projects.show', $testimonial->project) }}">
                                {{ $testimonial->project->name }}
                            </a>
                        </dd>
                        @endif

                        <dt class="col-sm-3">Display Order:</dt>
                        <dd class="col-sm-9">{{ $testimonial->order }}</dd>

                        <dt class="col-sm-3">Created:</dt>
                        <dd class="col-sm-9">{{ $testimonial->created_at->format('M d, Y h:i A') }}</dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9">{{ $testimonial->updated_at->format('M d, Y h:i A') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
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
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Client Information</h3>
                </div>
                <div class="card-body box-profile">
                    @if($testimonial->client_avatar)
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="{{ $testimonial->client_avatar }}" 
                                 alt="{{ $testimonial->client_name }}">
                        </div>
                    @endif

                    <h3 class="profile-username text-center">{{ $testimonial->client_name }}</h3>

                    @if($testimonial->client_position)
                        <p class="text-muted text-center">{{ $testimonial->client_position }}</p>
                    @endif

                    @if($testimonial->client_company)
                        <p class="text-center">
                            <strong>{{ $testimonial->client_company }}</strong>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
