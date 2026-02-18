<style>
    #global-page-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0;
        height: 3px;
        z-index: 99999;
        background: linear-gradient(90deg, #8b5cf6 0%, #a855f7 100%);
        box-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
        transition: width 0.25s ease, opacity 0.25s ease;
        opacity: 1;
        pointer-events: none;
    }

    #server-timing-debug-badge {
        position: fixed;
        right: 12px;
        bottom: 12px;
        z-index: 99999;
        padding: 6px 10px;
        border-radius: 9999px;
        background: rgba(30, 41, 59, 0.92);
        color: #e2e8f0;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        pointer-events: none;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
    }
</style>
<div id="global-page-progress" aria-hidden="true"></div>
<div id="server-timing-debug-badge" aria-live="polite">Server: -- ms</div>
<script>
    (function () {
        const progressBar = document.getElementById('global-page-progress');
        if (!progressBar) return;
        const timingBadge = document.getElementById('server-timing-debug-badge');

        let progress = 12;
        let timer = null;
        let activeRequests = 0;

        const updateTimingBadge = (durationMs) => {
            if (!timingBadge || durationMs === null || Number.isNaN(durationMs)) return;
            timingBadge.textContent = `Server: ${durationMs.toFixed(2)} ms`;
        };

        const parseServerTimingHeader = (value) => {
            if (!value) return null;

            const appMetric = value
                .split(',')
                .map((part) => part.trim())
                .find((part) => part.startsWith('app;') || part === 'app');

            if (!appMetric) return null;

            const durationPart = appMetric
                .split(';')
                .map((part) => part.trim())
                .find((part) => part.startsWith('dur='));

            if (!durationPart) return null;

            const duration = parseFloat(durationPart.replace('dur=', ''));
            return Number.isFinite(duration) ? duration : null;
        };

        const readNavigationServerTiming = () => {
            if (!timingBadge || !window.performance || !performance.getEntriesByType) return;
            const navigationEntries = performance.getEntriesByType('navigation');
            const navigation = navigationEntries && navigationEntries.length ? navigationEntries[0] : null;

            if (!navigation || !navigation.serverTiming) return;

            const appTiming = navigation.serverTiming.find((metric) => metric.name === 'app');
            if (appTiming && typeof appTiming.duration === 'number') {
                updateTimingBadge(appTiming.duration);
            }
        };

        const setProgress = (value) => {
            progress = Math.max(progress, Math.min(value, 95));
            progressBar.style.width = progress + '%';
        };

        const start = () => {
            progress = 12;
            progressBar.style.opacity = '1';
            progressBar.style.width = progress + '%';

            clearInterval(timer);
            timer = setInterval(() => {
                if (progress < 90) {
                    setProgress(progress + Math.max(1, (90 - progress) * 0.05));
                }
            }, 120);
        };

        const done = () => {
            clearInterval(timer);
            progressBar.style.width = '100%';
            setTimeout(() => {
                progressBar.style.opacity = '0';
                setTimeout(() => {
                    progressBar.style.width = '0';
                    progress = 12;
                }, 200);
            }, 180);
        };

        start();

        document.addEventListener('DOMContentLoaded', () => setProgress(55));
        window.addEventListener('load', () => {
            done();
            readNavigationServerTiming();
        });
        window.addEventListener('pageshow', done);

        document.addEventListener('submit', () => {
            start();
            setProgress(35);
        }, true);

        const requestStarted = () => {
            activeRequests += 1;
            if (activeRequests === 1) {
                start();
                setProgress(35);
            } else {
                setProgress(55);
            }
        };

        const requestFinished = () => {
            activeRequests = Math.max(0, activeRequests - 1);
            if (activeRequests === 0) {
                done();
            }
        };

        if (window.fetch) {
            const originalFetch = window.fetch.bind(window);
            window.fetch = function (...args) {
                requestStarted();
                return originalFetch(...args)
                    .then((response) => {
                        const duration = parseServerTimingHeader(response.headers.get('Server-Timing'));
                        updateTimingBadge(duration);
                        return response;
                    })
                    .finally(() => {
                        requestFinished();
                    });
            };
        }

        const originalXhrOpen = XMLHttpRequest.prototype.open;
        const originalXhrSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function (...args) {
            this.__hasProgressBinding = false;
            return originalXhrOpen.apply(this, args);
        };

        XMLHttpRequest.prototype.send = function (...args) {
            if (!this.__hasProgressBinding) {
                this.__hasProgressBinding = true;
                requestStarted();
                this.addEventListener('loadend', () => {
                    const duration = parseServerTimingHeader(this.getResponseHeader('Server-Timing'));
                    updateTimingBadge(duration);
                    requestFinished();
                }, { once: true });
            }

            return originalXhrSend.apply(this, args);
        };

        document.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target.closest('a[href]') : null;
            if (!target) return;

            const href = target.getAttribute('href');
            if (!href || href.startsWith('#')) return;
            if (target.getAttribute('target') === '_blank') return;
            if (target.hasAttribute('download')) return;

            const url = new URL(target.href, window.location.href);
            if (url.origin !== window.location.origin) return;
            if (url.pathname === window.location.pathname && url.search === window.location.search) return;

            start();
            setProgress(30);
        });
    })();
</script>
