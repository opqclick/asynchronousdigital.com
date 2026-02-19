<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Asynchronous Digital</title>
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

    <!-- About Hero Section -->
    <section class="pt-24 pb-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">About <span class="text-purple-500">Asynchronous Digital</span></h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                We are a dedicated cloud team specializing in modern software development, 
                committed to delivering exceptional digital solutions that drive innovation and growth.
            </p>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4 bg-slate-800/30">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-purple-500 mb-2">{{ $stats['projects_completed'] }}+</div>
                    <div class="text-slate-400">Projects Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-purple-500 mb-2">{{ $stats['active_clients'] }}+</div>
                    <div class="text-slate-400">Active Clients</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-purple-500 mb-2">{{ $stats['team_members'] }}+</div>
                    <div class="text-slate-400">Team Members</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-purple-500 mb-2">{{ $stats['years_experience'] }}+</div>
                    <div class="text-slate-400">Years Experience</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">Our Story</h2>
            <div class="space-y-6 text-slate-300 text-lg leading-relaxed">
                <p>
                    Asynchronous Digital was founded with a vision to transform how businesses approach software development. 
                    We believe in the power of cloud-based collaboration and the flexibility it brings to modern development teams.
                </p>
                <p>
                    Our team brings together diverse expertise in mobile development, web applications, DevOps, and design. 
                    We work asynchronously across time zones, allowing us to provide round-the-clock dedication to your projects 
                    while maintaining the highest standards of quality.
                </p>
                <p>
                    We don't just build software – we build partnerships. Every project is an opportunity to understand your 
                    unique challenges and create solutions that drive real business value. Our commitment to excellence and 
                    continuous improvement ensures that we stay at the forefront of technology trends.
                </p>
            </div>
        </div>
    </section>

    <!-- Our Values Section -->
    <section class="py-16 px-4 bg-slate-800/30">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center">Our Core Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-800/50 backdrop-blur-lg p-8 rounded-xl border border-slate-700 text-center">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-lightbulb text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Innovation</h3>
                    <p class="text-slate-400">
                        We constantly explore new technologies and methodologies to deliver cutting-edge solutions.
                    </p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-lg p-8 rounded-xl border border-slate-700 text-center">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-award text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Excellence</h3>
                    <p class="text-slate-400">
                        Quality is non-negotiable. We strive for excellence in every line of code we write.
                    </p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-lg p-8 rounded-xl border border-slate-700 text-center">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-handshake text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Partnership</h3>
                    <p class="text-slate-400">
                        Your success is our success. We work as an extension of your team.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    @if($teamMembers->count() > 0)
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center">Meet Our Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($teamMembers as $member)
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 overflow-hidden text-center hover:border-purple-500 transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-4 overflow-hidden">
                            @if($member->image_url)
                                <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-bold text-white">{{ substr($member->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $member->name }}</h3>
                        <p class="text-purple-400 text-sm mb-2">{{ $member->role_title ?? 'Team Member' }}</p>
                        @if($member->bio)
                            <p class="text-slate-400 text-sm">{{ Str::limit($member->bio, 120) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-16 px-4 bg-gradient-to-r from-purple-900/30 to-pink-900/30">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Work Together?</h2>
            <p class="text-lg text-slate-300 mb-8">
                Let's discuss how we can help bring your digital vision to life.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-purple-600 text-white rounded-full font-semibold hover:bg-purple-700 transition-colors duration-300">
                    Get In Touch <i class="fas fa-arrow-right ml-2"></i>
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
    </script>

</body>
</html>
