@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    @include('partials.page-load-progress')

    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')

    @auth
        <script>
            (function () {
                if (!('Notification' in window) || !('fetch' in window)) {
                    return;
                }

                const feedUrl = @json(route('notifications.unread-feed'));
                const userId = @json((string) auth()->id());
                const storageKey = 'desktop_notification_seen_ids_' + userId;
                const promptKey = 'desktop_notification_prompted_' + userId;
                const initiallySeen = new Set(JSON.parse(localStorage.getItem(storageKey) || '[]'));
                let hasInitialized = false;

                const persistSeen = function () {
                    localStorage.setItem(storageKey, JSON.stringify(Array.from(initiallySeen).slice(-300)));
                };

                const maybePromptPermission = function () {
                    if (Notification.permission !== 'default') {
                        return;
                    }

                    if (localStorage.getItem(promptKey) === '1') {
                        return;
                    }

                    localStorage.setItem(promptKey, '1');
                    Notification.requestPermission().catch(function () {});
                };

                const showDesktopNotification = function (item) {
                    if (Notification.permission !== 'granted') {
                        return;
                    }

                    const notification = new Notification('Asynchronous Digital', {
                        body: item.message || 'You have a new notification.',
                        tag: item.id,
                    });

                    notification.onclick = function () {
                        window.focus();
                        if (item.target_url) {
                            window.location.href = item.target_url;
                        }
                        notification.close();
                    };
                };

                const pollUnreadNotifications = function () {
                    fetch(feedUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Failed to fetch unread notifications');
                            }
                            return response.json();
                        })
                        .then(function (data) {
                            const items = Array.isArray(data.notifications) ? data.notifications : [];

                            if (!hasInitialized) {
                                items.forEach(function (item) {
                                    initiallySeen.add(item.id);
                                });
                                persistSeen();
                                hasInitialized = true;
                                return;
                            }

                            items
                                .slice()
                                .reverse()
                                .forEach(function (item) {
                                    if (!initiallySeen.has(item.id)) {
                                        initiallySeen.add(item.id);
                                        showDesktopNotification(item);
                                    }
                                });

                            persistSeen();
                        })
                        .catch(function () {});
                };

                setTimeout(maybePromptPermission, 1000);
                pollUnreadNotifications();
                setInterval(pollUnreadNotifications, 30000);
            })();
        </script>
    @endauth
@stop
