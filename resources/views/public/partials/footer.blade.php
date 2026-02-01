<footer class="bg-slate-800/50 border-t border-slate-700">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="mb-3">
                    <x-logo />
                </div>
                <p class="text-slate-400 mb-4">
                    A dedicated cloud team specializing in modern software development.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-slate-400 hover:text-purple-500 transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-purple-500 transition-colors">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-purple-500 transition-colors">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-purple-500 transition-colors">Home</a></li>
                    <li><a href="{{ route('about') }}" class="text-slate-400 hover:text-purple-500 transition-colors">About</a></li>
                    <li><a href="{{ route('services') }}" class="text-slate-400 hover:text-purple-500 transition-colors">Services</a></li>
                    <li><a href="{{ route('portfolio') }}" class="text-slate-400 hover:text-purple-500 transition-colors">Portfolio</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-white font-semibold mb-3">Contact</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('contact') }}" class="hover:text-purple-500 transition-colors">Get In Touch</a></li>
                    <li><a href="mailto:asynchronousd@gmail.com" class="hover:text-purple-500 transition-colors">asynchronousd@gmail.com</a></li>
                    @auth
                    <li><a href="{{ route('dashboard') }}" class="hover:text-purple-500 transition-colors">Dashboard</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-700 pt-6 text-center">
            <p class="text-sm text-slate-400">© {{ date('Y') }} Asynchronous Digital. All rights reserved.</p>
        </div>
    </div>
</footer>
