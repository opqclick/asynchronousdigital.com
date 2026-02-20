<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        if (html.classList.contains('dark-mode')) {
            html.classList.remove('dark-mode');
            localStorage.setItem('ad_dark_mode', '0');
        } else {
            html.classList.add('dark-mode');
            localStorage.setItem('ad_dark_mode', '1');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('ad_dark_mode') === '1') {
            document.documentElement.classList.add('dark-mode');
        }
    });
</script>
<style>
    .dark-mode {
        background: #18191a !important;
        color: #e4e6eb !important;
    }
    .dark-mode .navbar, .dark-mode .sidebar, .dark-mode .content-wrapper {
        background: #242526 !important;
        color: #e4e6eb !important;
    }
    .dark-mode .card { background: #242526 !important; color: #e4e6eb !important; }
    .dark-toggle-btn {
        position: fixed;
        right: 32px;
        bottom: 56px;
        z-index: 100000;
        background: #222;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 6px 18px;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
</style>
<button class="dark-toggle-btn" onclick="toggleDarkMode()">
    <span id="darkModeText">🌙 Dark Mode</span>
</button>
<script>
    function updateDarkModeText() {
        const btn = document.getElementById('darkModeText');
        if (document.documentElement.classList.contains('dark-mode')) {
            btn.textContent = '☀️ Light Mode';
        } else {
            btn.textContent = '🌙 Dark Mode';
        }
    }
    document.querySelector('.dark-toggle-btn').addEventListener('click', updateDarkModeText);
    document.addEventListener('DOMContentLoaded', updateDarkModeText);
</script>
