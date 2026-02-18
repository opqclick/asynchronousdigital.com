<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Asynchronous Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #a855f7;
            transition: width 0.3s ease;
        }
        .nav-link:hover:after,
        .nav-link.active:after {
            width: 100%;
        }
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .mobile-menu.active {
            max-height: 500px;
        }
    </style>
</head>
<body class="bg-slate-900 text-white">

    @include('partials.page-load-progress')

    <!-- Navigation Header -->
    @include('public.partials.nav')

    <!-- Services Hero Section -->
    <section class="pt-24 pb-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">Our <span class="text-purple-500">Services</span></h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Comprehensive software development services tailored to your business needs. 
                From concept to deployment, we've got you covered.
            </p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300 transform hover:scale-105">
                    <!-- Service Icon -->
                    @if($service->icon)
                    <div class="text-5xl mb-6 text-purple-500">
                        {!! $service->icon !!}
                    </div>
                    @endif

                    <!-- Service Title -->
                    <h3 class="text-2xl font-bold mb-3">{{ $service->title }}</h3>

                    <!-- Short Description -->
                    <p class="text-slate-400 mb-6 leading-relaxed">
                        {{ $service->short_description }}
                    </p>

                    <!-- Features List -->
                    @if($service->features && count($service->features) > 0)
                    <ul class="space-y-2 mb-6">
                        @foreach($service->features as $feature)
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <!-- Pricing -->
                    @if($service->price_display)
                    <div class="mt-6 pt-6 border-t border-slate-700">
                        <p class="text-lg font-semibold text-purple-400">{{ $service->price_display }}</p>
                    </div>
                    @endif

                    <!-- Full Description Toggle -->
                    @if($service->full_description)
                    <div class="mt-4">
                        <button onclick="toggleDescription({{ $service->id }})" class="text-purple-400 hover:text-purple-300 text-sm font-medium">
                            Read More <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                        <div id="description-{{ $service->id }}" class="hidden mt-4 pt-4 border-t border-slate-700">
                            <p class="text-slate-300 text-sm leading-relaxed">{{ $service->full_description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <!-- Default Services when database is empty -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Android Development -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-purple-500">
                        <i class="fab fa-android"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Android Development</h3>
                    <p class="text-slate-400 mb-6">Native Android applications with exceptional performance and user experience.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                            <span>Native Kotlin/Java development</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                            <span>Material Design implementation</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                            <span>Google Play Store deployment</span>
                        </li>
                    </ul>
                </div>

                <!-- Flutter Development -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-blue-500">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Flutter Development</h3>
                    <p class="text-slate-400 mb-6">Cross-platform mobile apps for iOS and Android from a single codebase.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Single codebase for both platforms</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Beautiful, customizable UI</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Fast development & hot reload</span>
                        </li>
                    </ul>
                </div>

                <!-- Web Applications -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-green-500">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Web Applications</h3>
                    <p class="text-slate-400 mb-6">Powerful, scalable web applications with modern frameworks.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>React, Vue, Laravel development</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>RESTful API development</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Database design & optimization</span>
                        </li>
                    </ul>
                </div>

                <!-- Website Creation -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-yellow-500">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Website Creation</h3>
                    <p class="text-slate-400 mb-6">Responsive, high-performing websites tailored to your needs.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                            <span>Responsive design</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                            <span>SEO optimization</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                            <span>Content management systems</span>
                        </li>
                    </ul>
                </div>

                <!-- DevOps -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-red-500">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">DevOps</h3>
                    <p class="text-slate-400 mb-6">Continuous integration and delivery pipelines for streamlined development.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>CI/CD pipeline setup</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>Cloud infrastructure management</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>Docker & Kubernetes</span>
                        </li>
                    </ul>
                </div>

                <!-- Design Support -->
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition-all duration-300">
                    <div class="text-5xl mb-6 text-cyan-500">
                        <i class="fas fa-pen-nib"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Design Support</h3>
                    <p class="text-slate-400 mb-6">Comprehensive UI/UX design and branding solutions.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-cyan-500 mt-1 mr-2"></i>
                            <span>UI/UX design</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-cyan-500 mt-1 mr-2"></i>
                            <span>Brand identity development</span>
                        </li>
                        <li class="flex items-start text-slate-300 text-sm">
                            <i class="fas fa-check-circle text-cyan-500 mt-1 mr-2"></i>
                            <span>Prototyping & wireframing</span>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 bg-gradient-to-r from-purple-900/30 to-pink-900/30">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="text-lg text-slate-300 mb-8">
                Let's discuss your project and find the perfect solution for your needs.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-purple-600 text-white rounded-full font-semibold hover:bg-purple-700 transition-colors duration-300">
                    Request a Quote <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('portfolio') }}" class="px-8 py-4 bg-slate-800 text-white rounded-full font-semibold hover:bg-slate-700 transition-colors duration-300">
                    View Our Work
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('public.partials.footer')

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu?.classList.toggle('active');
        });

        // Toggle full description
        function toggleDescription(serviceId) {
            const descElement = document.getElementById('description-' + serviceId);
            if (descElement) {
                descElement.classList.toggle('hidden');
            }
        }
    </script>

</body>
</html>
