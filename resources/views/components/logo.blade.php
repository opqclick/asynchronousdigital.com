<style>
.logo-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.logo-container:hover {
    transform: translateY(-2px);
}

.logo-image {
    height: 40px;
    width: auto;
    transition: all 0.3s ease;
}

.logo-container:hover .logo-image {
    filter: brightness(1.1) drop-shadow(0 0 10px rgba(168, 85, 247, 0.5));
}

@media (max-width: 640px) {
    .logo-image {
        height: 36px;
    }
}
</style>

<a href="{{ route('home') }}" class="logo-container">
    <img src="{{ asset('logo.png') }}" alt="Asynchronous Digital" class="logo-image">
</a>
