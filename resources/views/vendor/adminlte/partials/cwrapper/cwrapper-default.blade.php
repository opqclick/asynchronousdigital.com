@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\preloaderHelper')

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

{{-- Default Content Wrapper --}}
<div class="{{ $layoutHelper->makeContentWrapperClasses() }}">

    {{-- Preloader Animation (cwrapper mode) --}}
    @if($preloaderHelper->isPreloaderEnabled('cwrapper'))
        @include('adminlte::partials.common.preloader')
    @endif

    {{-- Content Header --}}
    @hasSection('content_header')
        <div class="content-header">
            <div class="{{ config('adminlte.classes_content_header') ?: $def_container_class }}">
                @yield('content_header')
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="content">
        <div class="{{ config('adminlte.classes_content') ?: $def_container_class }}">
            @if(auth()->check() && auth()->user()->roles->count() > 1)
                <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between" role="alert">
                    <div>
                        <strong><i class="fas fa-user-tag"></i> Active Role:</strong>
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role?->name ?? 'N/A')) }}
                    </div>
                    <form method="POST" action="{{ route('profile.switch-role') }}" class="d-flex align-items-center mt-2 mt-md-0">
                        @csrf
                        <label for="top_active_role_id" class="mb-0 mr-2">Switch</label>
                        <select id="top_active_role_id" name="active_role_id" class="form-control form-control-sm mr-2" style="min-width: 180px;">
                            @foreach(auth()->user()->roles as $switchRole)
                                <option value="{{ $switchRole->id }}" {{ auth()->user()->role && auth()->user()->role->id === $switchRole->id ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $switchRole->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    </form>
                </div>
            @endif

            @if(auth()->check() && session()->has('impersonator_id'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-user-secret"></i> Impersonation Mode:</strong>
                    You are currently logged in as <strong>{{ auth()->user()->name }}</strong>.
                    <a href="{{ route('admin.impersonation.leave') }}" class="btn btn-sm btn-dark ml-2">
                        <i class="fas fa-undo"></i> Back to Admin Panel
                    </a>
                </div>
            @endif

            @stack('content')
            @yield('content')
        </div>
    </div>

</div>
