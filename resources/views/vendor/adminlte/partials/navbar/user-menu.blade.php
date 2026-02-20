{{-- AdminLTE Dark / Light mode toggle --}}
<li class="nav-item" title="Toggle dark / light mode">
    <a id="ad-darkmode-btn" class="nav-link" href="#" role="button" onclick="adToggleDarkMode(event)"
        style="font-size:1.1rem; padding: 0.5rem 0.75rem;">
        <i id="ad-darkmode-icon" class="fas fa-moon"></i>
    </a>
</li>

<script>
    (function () {
        var STORAGE_KEY = 'ad_dark_mode';

        function applyMode(dark) {
            if (dark) {
                document.body.classList.add('dark-mode');
                document.body.classList.add('layout-dark');
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.remove('layout-dark');
            }
            syncIcon(dark);
        }

        function syncIcon(dark) {
            var icon = document.getElementById('ad-darkmode-icon');
            if (!icon) return;
            if (dark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        window.adToggleDarkMode = function (e) {
            e.preventDefault();
            var isDark = document.body.classList.contains('dark-mode');
            var next = !isDark;
            localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
            applyMode(next);
        };

        // Apply saved preference as early as possible to avoid flash
        document.addEventListener('DOMContentLoaded', function () {
            var saved = localStorage.getItem(STORAGE_KEY);
            applyMode(saved === '1');
        });
    })();
</script>