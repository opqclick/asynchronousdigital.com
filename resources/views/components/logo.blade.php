<style>
.logo-text {
    font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 700;
    font-size: 1.75rem;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
    transition: all 0.3s ease;
}

.logo-text:hover {
    transform: translateY(-2px);
    filter: brightness(1.2);
}

.logo-text::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #8b5cf6 100%);
    filter: blur(20px);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.logo-text:hover::before {
    opacity: 0.3;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #a855f7, #ec4899);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    color: white;
    font-size: 1.25rem;
    letter-spacing: -0.05em;
}

@media (max-width: 640px) {
    .logo-text {
        font-size: 1.5rem;
    }
    .logo-icon {
        width: 32px;
        height: 32px;
        font-size: 1.1rem;
    }
}
</style>

<a href="{{ route('home') }}" class="logo-container">
    <div class="logo-icon">AD</div>
    <span class="logo-text">Asynchronous Digital</span>
</a>
