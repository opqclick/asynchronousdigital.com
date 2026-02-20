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
{{-- Apply saved dark mode preference immediately to avoid flash --}}
<script>
    (function () {
        if (localStorage.getItem('ad_dark_mode') === '1') {
            document.body.classList.add('dark-mode');
        }
    })();
</script>

{{-- Dark mode toggle logic (icon sync) --}}
<script>
    var STORAGE_KEY = 'ad_dark_mode';

    window.adToggleDarkMode = function (e) {
        e && e.preventDefault();
        var isDark = document.body.classList.contains('dark-mode');
        var next = !isDark;
        if (next) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
        syncDarkModeIcon(next);
    };

    function syncDarkModeIcon(isDark) {
        var icon = document.getElementById('ad-darkmode-icon');
        if (!icon) return;
        if (isDark) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        syncDarkModeIcon(document.body.classList.contains('dark-mode'));
    });
</script>

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
@parent
<script>
    // Prevent browser back to dashboard after logout (AdminLTE layout)
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        fetch('/api/user', { credentials: 'same-origin' })
            .then(r => {
                if (r.status === 401) {
                    window.location.href = '/login';
                }
            })
            .catch(() => {
                window.location.href = '/login';
            });
    }
</script>

@stack('js')
@yield('js')

<script>
    (function () {
        const hasSwal = !!(window.Swal && typeof window.Swal.fire === 'function');
        const allowForceDelete = @json(auth()->check() && auth()->user()->isAdmin());

        const fireToast = function (options) {
            if (!hasSwal) {
                return Promise.resolve();
            }

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

        const flashSuccess = @json(session('success'));
        const flashError = @json(session('error'));

        if (flashSuccess) {
            fireToast({ icon: 'success', title: flashSuccess, timer: 4500 });
        }

        if (flashError) {
            fireToast({ icon: 'error', title: flashError, timer: 6500, showConfirmButton: true });
        }

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
            const signalCancel = function () {
                window.dispatchEvent(new Event('app:request-cancelled'));
            };

            if (!hasSwal) {
                if (window.confirm(message || 'Please confirm this action.')) {
                    onConfirm();
                } else {
                    signalCancel();
                }

                return;
            }

            try {
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
                    } else {
                        signalCancel();
                    }
                }).catch(function () {
                    if (window.confirm(message || 'Please confirm this action.')) {
                        onConfirm();
                    } else {
                        signalCancel();
                    }
                });
            } catch (error) {
                if (window.confirm(message || 'Please confirm this action.')) {
                    onConfirm();
                } else {
                    signalCancel();
                }
            }
        };

        const isDeleteOperation = function (form, trigger, message) {
            if (form) {
                const methodInput = form.querySelector('input[name="_method"]');
                if (methodInput && String(methodInput.value || '').toUpperCase() === 'DELETE') {
                    return true;
                }

                const directMethod = String(form.getAttribute('method') || '').toUpperCase();
                if (directMethod === 'DELETE') {
                    return true;
                }
            }

            const triggerMessage = trigger ? trigger.getAttribute('data-confirm-message') : '';
            const text = String(message || triggerMessage || '').toLowerCase();
            return text.includes('delete');
        };

        const setDeleteMode = function (form, mode) {
            if (!form) {
                return;
            }

            let input = form.querySelector('input[name="delete_mode"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_mode';
                form.appendChild(input);
            }

            input.value = mode;
        };

        const isDeleteForm = function (form) {
            if (!form) {
                return false;
            }

            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && String(methodInput.value || '').toUpperCase() === 'DELETE') {
                return true;
            }

            const directMethod = String(form.getAttribute('method') || '').toUpperCase();
            return directMethod === 'DELETE';
        };

        const openDeleteChoiceDialog = function (message, form, onConfirm) {
            const signalCancel = function () {
                window.dispatchEvent(new Event('app:request-cancelled'));
            };

            if (!hasSwal) {
                const choice = window.prompt(
                    (message || 'Choose delete type') + "\nType 'soft' for recoverable delete or 'permanent' for irreversible delete.",
                    'soft'
                );

                if (!choice) {
                    signalCancel();
                    return;
                }

                const normalized = String(choice).trim().toLowerCase();
                if (normalized === 'permanent' || normalized === 'force') {
                    setDeleteMode(form, 'force');
                    onConfirm();
                    return;
                }

                if (normalized === 'soft') {
                    setDeleteMode(form, 'soft');
                    onConfirm();
                    return;
                }

                window.alert("Invalid choice. Please type 'soft' or 'permanent'.");
                signalCancel();
                return;
            }

            const openNativeDeletePrompt = function () {
                const choice = window.prompt(
                    (message || 'Choose delete type') + "\nType 'soft' for recoverable delete or 'permanent' for irreversible delete.",
                    'soft'
                );

                if (!choice) {
                    signalCancel();
                    return;
                }

                const normalized = String(choice).trim().toLowerCase();
                if (normalized === 'permanent' || normalized === 'force') {
                    setDeleteMode(form, 'force');
                    onConfirm();
                    return;
                }

                if (normalized === 'soft') {
                    setDeleteMode(form, 'soft');
                    onConfirm();
                    return;
                }

                setDeleteMode(form, 'soft');
                onConfirm();
            };

            try {
                window.SWal.fire({
                    title: 'Choose delete type',
                    text: message || 'You can soft delete (recoverable) or permanently delete (irreversible).',
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Permanent Delete',
                    denyButtonText: 'Soft Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        setDeleteMode(form, 'force');
                        onConfirm();
                        return;
                    }

                    if (result.isDenied) {
                        setDeleteMode(form, 'soft');
                        onConfirm();
                        return;
                    }

                    signalCancel();
                }).catch(function () {
                    openNativeDeletePrompt();
                });
            } catch (error) {
                openNativeDeletePrompt();
            }
        };

        const resolveDeleteModeByNativePrompt = function (message, form) {
            if (!allowForceDelete) {
                const softOnlyMessage = (message || 'Are you sure you want to delete this item?') + "\n\nNote: Your role can only perform soft delete.";
                if (!window.confirm(softOnlyMessage)) {
                    window.dispatchEvent(new Event('app:request-cancelled'));
                    return false;
                }

                setDeleteMode(form, 'soft');
                return true;
            }

            const choice = window.prompt(
                (message || 'Choose delete type') + "\nType 'soft' for recoverable delete or 'permanent' for irreversible delete.",
                'soft'
            );

            if (!choice) {
                window.dispatchEvent(new Event('app:request-cancelled'));
                return false;
            }

            const normalized = String(choice).trim().toLowerCase();
            if (normalized === 'permanent' || normalized === 'force') {
                setDeleteMode(form, 'force');
                return true;
            }

            setDeleteMode(form, 'soft');
            return true;
        };

        document.addEventListener('click', function (event) {
            const clickTarget = event.target instanceof Element ? event.target : null;
            if (!clickTarget) {
                return;
            }

            const trigger = clickTarget.closest('button[onclick*="confirm("], input[type="submit"][onclick*="confirm("], a[onclick*="confirm("], button[data-confirm-message], input[type="submit"][data-confirm-message], a[data-confirm-message]');
            if (!trigger) {
                return;
            }

            const inlineCode = trigger.getAttribute('onclick') || '';
            const message = parseConfirmMessage(inlineCode) || trigger.getAttribute('data-confirm-message') || 'Are you sure you want to continue?';
            const form = trigger.form || trigger.closest('form');
            const href = trigger.getAttribute('href');

            const deleteOperation = isDeleteOperation(form, trigger, message);
            if (deleteOperation) {
                if (!resolveDeleteModeByNativePrompt(message, form)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const complete = function () {
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
            };

            try {
                openConfirmDialog(message, complete);
            } catch (error) {
                if (form) {
                    setDeleteMode(form, 'soft');
                    form.dataset.swalConfirmBypass = '1';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                } else if (trigger.tagName === 'A' && href) {
                    window.location.href = href;
                }
            }
        }, true);

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const formAction = String(form.getAttribute('action') || '');
            if (formAction.includes('/recycle-bin/')) {
                return;
            }

            if (form.dataset.swalConfirmBypass === '1') {
                delete form.dataset.swalConfirmBypass;
                return;
            }

            const inlineCode = form.getAttribute('onsubmit') || '';
            const deleteForm = isDeleteForm(form);
            const hasLegacyConfirm = inlineCode.includes('confirm(');
            const hasDeclarativeConfirm = !!form.getAttribute('data-confirm-message');
            if (!deleteForm && !hasLegacyConfirm && !hasDeclarativeConfirm) {
                return;
            }

            const message = parseConfirmMessage(inlineCode)
                || form.getAttribute('data-confirm-message')
                || (deleteForm
                    ? 'Choose delete type: soft delete (recoverable) or permanent delete (irreversible).'
                    : 'Are you sure you want to continue?');

            if (deleteForm) {
                if (!resolveDeleteModeByNativePrompt(message, form)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const complete = function () {
                form.dataset.swalConfirmBypass = '1';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            };

            try {
                openConfirmDialog(message, complete);
            } catch (error) {
                setDeleteMode(form, 'soft');
                form.dataset.swalConfirmBypass = '1';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        }, true);

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(function (form) {
                if (!(form instanceof HTMLFormElement) || !isDeleteForm(form)) {
                    return;
                }

                const existing = form.querySelector('input[name="delete_mode"]');
                if (!existing) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete_mode';
                    input.value = 'soft';
                    form.appendChild(input);
                }
            });
        });
    })();
</script>

@auth
    <script>
        (function () {
            if (!window.fetch || !(window.SWal && typeof window.SWal.fire === 'function')) {
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
                window.SWal.fire({
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
                    .catch(function () { });
            };

            poll();
            setInterval(poll, 15000);
        })();
    </script>
@endauth
@stop