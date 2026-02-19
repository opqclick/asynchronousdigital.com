<nav class="fixed top-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <x-logo />
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="nav-link text-slate-300 hover:text-white px-3 py-2 {{ request()->routeIs('home') ? 'active text-white' : '' }}">
                    Home
                </a>
                <a href="{{ route('about') }}" class="nav-link text-slate-300 hover:text-white px-3 py-2 {{ request()->routeIs('about') ? 'active text-white' : '' }}">
                    About
                </a>
                <a href="{{ route('services') }}" class="nav-link text-slate-300 hover:text-white px-3 py-2 {{ request()->routeIs('services') ? 'active text-white' : '' }}">
                    Services
                </a>
                <a href="{{ route('portfolio') }}" class="nav-link text-slate-300 hover:text-white px-3 py-2 {{ request()->routeIs('portfolio') ? 'active text-white' : '' }}">
                    Portfolio
                </a>
                <a href="{{ route('contact') }}" class="nav-link text-slate-300 hover:text-white px-3 py-2 {{ request()->routeIs('contact') ? 'active text-white' : '' }}">
                    Contact
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link text-purple-400 hover:text-purple-300 px-3 py-2">
                        Dashboard
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-slate-300 hover:text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="mobile-menu md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded {{ request()->routeIs('home') ? 'bg-slate-800 text-white' : '' }}">
                    Home
                </a>
                <a href="{{ route('about') }}" class="block text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded {{ request()->routeIs('about') ? 'bg-slate-800 text-white' : '' }}">
                    About
                </a>
                <a href="{{ route('services') }}" class="block text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded {{ request()->routeIs('services') ? 'bg-slate-800 text-white' : '' }}">
                    Services
                </a>
                <a href="{{ route('portfolio') }}" class="block text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded {{ request()->routeIs('portfolio') ? 'bg-slate-800 text-white' : '' }}">
                    Portfolio
                </a>
                <a href="{{ route('contact') }}" class="block text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded {{ request()->routeIs('contact') ? 'bg-slate-800 text-white' : '' }}">
                    Contact
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block text-purple-400 hover:text-purple-300 hover:bg-slate-800 px-3 py-2 rounded">
                        Dashboard
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
