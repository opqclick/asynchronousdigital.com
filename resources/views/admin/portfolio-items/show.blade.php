@extends('adminlte::page')

@section('title', 'Portfolio Item')

@section('content_header')
<h1>Portfolio Item</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $portfolioItem->title }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.portfolio-items.edit', $portfolioItem) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.portfolio-items.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($portfolioItem->image_url)
            <div class="mb-3">
                <img src="{{ $portfolioItem->image_url }}" alt="{{ $portfolioItem->title }}" class="img-fluid rounded"
                    style="max-height: 300px;">
            </div>
        @endif

        <table class="table table-bordered">
            <tr>
                <th style="width: 200px;">Title</th>
                <td>{{ $portfolioItem->title }}</td>
            </tr>
            <tr>
                <th>Client</th>
                <td>{{ $portfolioItem->client_name ?: '—' }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $portfolioItem->description ?: '—' }}</td>
            </tr>
            <tr>
                <th>Project URL</th>
                <td>
                    @if($portfolioItem->project_url)
                        <a href="{{ $portfolioItem->project_url }}" target="_blank">{{ $portfolioItem->project_url }}</a>
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <th>Technologies</th>
                <td>
                    @if($portfolioItem->tech_tags)
                        @foreach($portfolioItem->tech_tags as $tag)
                            <span class="badge badge-info">{{ $tag }}</span>
                        @endforeach
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <th>Display Order</th>
                <td>{{ $portfolioItem->display_order }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge badge-{{ $portfolioItem->is_published ? 'success' : 'secondary' }}">
                        {{ $portfolioItem->is_published ? 'Published' : 'Draft' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Created</th>
                <td>{{ $portfolioItem->created_at->format('d M Y, H:i') }}</td>
            </tr>
        </table>
    </div>
</div>
@stop