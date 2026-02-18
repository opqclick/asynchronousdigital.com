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

    <script>
        (function () {
            if (!(window.Swal && typeof window.Swal.fire === 'function')) {
                return;
            }

            const fireToast = function (options) {
                const config = Object.assign({
                    toast: true,
                    position: 'top-end',
                    timer: 3500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }, options || {});

                return window.Swal.fire(config);
            };

            window.appToast = fireToast;

            window.toastr = {
                success: function (message, title) {
                    return fireToast({ icon: 'success', title: title || message || 'Success' });
                },
                error: function (message, title) {
                    return fireToast({ icon: 'error', title: title || message || 'Error', timer: 4500 });
                },
                info: function (message, title) {
                    return fireToast({ icon: 'info', title: title || message || 'Info' });
                },
                warning: function (message, title) {
                    return fireToast({ icon: 'warning', title: title || message || 'Warning', timer: 4500 });
                }
            };

            const parseConfirmMessage = function (inlineCode) {
                if (!inlineCode) {
                    return null;
                }

                const match = inlineCode.match(/confirm\((['"`])([\s\S]*?)\1\)/);
                if (!match || !match[2]) {
                    return null;
                }

                return match[2]
                    .replace(/\\'/g, "'")
                    .replace(/\\"/g, '"')
                    .replace(/\\`/g, '`');
            };

            const resolveConfirmUi = function (message) {
                const text = (message || '').toLowerCase();

                if (text.includes('delete')) {
                    return {
                        title: 'Delete this item?',
                        confirmButtonText: 'Yes, delete'
                    };
                }

                if (text.includes('invitation') || text.includes('invite')) {
                    return {
                        title: 'Send invitation?',
                        confirmButtonText: 'Yes, send invitation'
                    };
                }

                if (text.includes('login as') || text.includes('impersonate')) {
                    return {
                        title: 'Switch user session?',
                        confirmButtonText: 'Yes, continue as user'
                    };
                }

                if (text.includes('received') || text.includes('salary payment')) {
                    return {
                        title: 'Confirm payment receipt?',
                        confirmButtonText: 'Yes, confirm receipt'
                    };
                }

                return {
                    title: 'Are you sure?',
                    confirmButtonText: 'Yes, continue'
                };
            };

            const openConfirmDialog = function (message, onConfirm) {
                const confirmUi = resolveConfirmUi(message);

                window.Swal.fire({
                    title: confirmUi.title,
                    text: message || 'Please confirm this action.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: confirmUi.confirmButtonText,
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        onConfirm();
                    }
                });
            };

            document.addEventListener('click', function (event) {
                const trigger = event.target.closest('button[onclick*="confirm("], input[type="submit"][onclick*="confirm("], a[onclick*="confirm("], button[data-confirm-message], input[type="submit"][data-confirm-message], a[data-confirm-message]');
                if (!trigger) {
                    return;
                }

                const inlineCode = trigger.getAttribute('onclick') || '';
                const message = parseConfirmMessage(inlineCode) || trigger.getAttribute('data-confirm-message') || 'Are you sure you want to continue?';
                const form = trigger.form || trigger.closest('form');
                const href = trigger.getAttribute('href');

                event.preventDefault();
                event.stopImmediatePropagation();

                openConfirmDialog(message, function () {
                    if (form) {
                        form.dataset.swalConfirmBypass = '1';
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                        return;
                    }

                    if (trigger.tagName === 'A' && href) {
                        window.location.href = href;
                    }
                });
            }, true);

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (form.dataset.swalConfirmBypass === '1') {
                    delete form.dataset.swalConfirmBypass;
                    return;
                }

                const inlineCode = form.getAttribute('onsubmit') || '';
                const hasLegacyConfirm = inlineCode.includes('confirm(');
                const hasDeclarativeConfirm = !!form.getAttribute('data-confirm-message');
                if (!hasLegacyConfirm && !hasDeclarativeConfirm) {
                    return;
                }

                const message = parseConfirmMessage(inlineCode) || form.getAttribute('data-confirm-message') || 'Are you sure you want to continue?';

                event.preventDefault();
                event.stopImmediatePropagation();

                openConfirmDialog(message, function () {
                    form.dataset.swalConfirmBypass = '1';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                });
            }, true);
        })();
    </script>

    @auth
        <script>
            (function () {
                if (!window.fetch || !(window.Swal && typeof window.Swal.fire === 'function')) {
                    return;
                }

                const feedUrl = @json(route('notifications.unread-feed'));
                const userId = @json((string) auth()->id());
                const storageKey = 'swal_notification_seen_ids_' + userId;
                const seenIds = new Set(JSON.parse(localStorage.getItem(storageKey) || '[]'));

                const persistSeen = function () {
                    localStorage.setItem(storageKey, JSON.stringify(Array.from(seenIds).slice(-300)));
                };

                const showToast = function (item) {
                    window.Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: item.message || 'New notification',
                        showConfirmButton: true,
                        confirmButtonText: 'Open',
                        timer: 8000,
                        timerProgressBar: true,
                    }).then(function (result) {
                        if (result.isConfirmed && item.target_url) {
                            window.location.href = item.target_url;
                        }
                    });
                };

                const poll = function () {
                    fetch(feedUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Feed request failed');
                            }
                            return response.json();
                        })
                        .then(function (payload) {
                            const notifications = Array.isArray(payload.notifications) ? payload.notifications : [];

                            notifications
                                .slice()
                                .reverse()
                                .forEach(function (item) {
                                    if (!seenIds.has(item.id)) {
                                        seenIds.add(item.id);
                                        showToast(item);
                                    }
                                });

                            persistSeen();
                        })
                        .catch(function () {});
                };

                poll();
                setInterval(poll, 15000);
            })();
        </script>
    @endauth
@stop
