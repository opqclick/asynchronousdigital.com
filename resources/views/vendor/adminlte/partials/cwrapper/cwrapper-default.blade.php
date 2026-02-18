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
