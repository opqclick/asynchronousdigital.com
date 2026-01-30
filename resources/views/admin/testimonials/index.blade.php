@extends('adminlte::page')

@section('title', 'Testimonials')

@section('content_header')
    <h1>Testimonials</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Testimonials</h3>
            <div class="card-tools">
                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New Testimonial
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="testimonials-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Client Name</th>
                        <th>Company</th>
                        <th>Rating</th>
                        <th>Project</th>
                        <th>Featured</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testimonial)
                        <tr>
                            <td>{{ $testimonial->order }}</td>
                            <td>
                                {{ $testimonial->client_name }}
                                @if($testimonial->client_position)
                                    <br><small class="text-muted">{{ $testimonial->client_position }}</small>
                                @endif
                            </td>
                            <td>{{ $testimonial->client_company }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </td>
                            <td>
                                @if($testimonial->project)
                                    <a href="{{ route('admin.projects.show', $testimonial->project) }}">
                                        {{ $testimonial->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $testimonial->is_featured ? 'warning' : 'secondary' }}">
                                    {{ $testimonial->is_featured ? 'Featured' : 'Regular' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $testimonial->is_published ? 'success' : 'secondary' }}">
                                    {{ $testimonial->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.testimonials.show', $testimonial) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-xs btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#testimonials-table').DataTable({
            order: [[0, 'asc']]
        });
    });
</script>
@stop
