@extends('adminlte::page')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="{{ $service->icon }} fa-2x mr-2"></i> {{ $service->title }}</h3>
            <div class="card-tools">
                <span class="badge badge-{{ $service->is_active ? 'success' : 'secondary' }}">
                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Display Order:</dt>
                <dd class="col-sm-9">{{ $service->order }}</dd>

                <dt class="col-sm-3">Short Description:</dt>
                <dd class="col-sm-9">{{ $service->short_description }}</dd>

                @if($service->full_description)
                <dt class="col-sm-3">Full Description:</dt>
                <dd class="col-sm-9">{{ $service->full_description }}</dd>
                @endif

                <dt class="col-sm-3">Pricing Model:</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-{{ $service->pricing_model == 'fixed' ? 'success' : ($service->pricing_model == 'hourly' ? 'info' : 'warning') }}">
                        {{ ucfirst($service->pricing_model) }}
                    </span>
                </dd>

                @if($service->base_price)
                <dt class="col-sm-3">Base Price:</dt>
                <dd class="col-sm-9">${{ number_format($service->base_price, 2) }}</dd>
                @endif

                @if($service->price_display)
                <dt class="col-sm-3">Price Display:</dt>
                <dd class="col-sm-9">{{ $service->price_display }}</dd>
                @endif

                @if($service->features && count($service->features) > 0)
                <dt class="col-sm-3">Features:</dt>
                <dd class="col-sm-9">
                    <ul>
                        @foreach($service->features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </dd>
                @endif

                <dt class="col-sm-3">Created:</dt>
                <dd class="col-sm-9">{{ $service->created_at->format('M d, Y h:i A') }}</dd>

                <dt class="col-sm-3">Last Updated:</dt>
                <dd class="col-sm-9">{{ $service->updated_at->format('M d, Y h:i A') }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Service
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display:inline;" 
                  data-confirm-message="Are you sure you want to delete this service?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Service
                </button>
            </form>
        </div>
    </div>
@stop
