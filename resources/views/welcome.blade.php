<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asynchronous Digital - Modern Software Development</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        .nav-link { position: relative; transition: color 0.3s ease; }
        .nav-link:after { content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 0; background-color: #a855f7; transition: width 0.3s ease; }
        .nav-link:hover:after, .nav-link.active:after { width: 100%; }
        .mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-in-out; }
        .mobile-menu.active { max-height: 500px; }
        .dot-pulse { animation: dot-pulse 1.5s infinite ease-in-out; }
        .dot-pulse-1 { animation-delay: 0s; }
        .dot-pulse-2 { animation-delay: 0.2s; }
        @keyframes dot-pulse { 0%, 100% { transform: scale(0.8); opacity: 0.5; } 50% { transform: scale(1.2); opacity: 1; } }
    </style>
</head>
<body class="bg-slate-900 text-white">

    <!-- Page Loader -->
    <div id="loader" class="fixed inset-0 bg-slate-900 z-50 flex items-center justify-center transition-opacity duration-500">
        <div class="flex space-x-2">
            <div class="w-4 h-4 rounded-full bg-purple-500 dot-pulse dot-pulse-1"></div>
            <div class="w-4 h-4 rounded-full bg-purple-500 dot-pulse dot-pulse-2"></div>
            <div class="w-4 h-4 rounded-full bg-purple-500 dot-pulse"></div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="#home" class="text-2xl font-bold"><span class="text-purple-500">Async</span>Digital</a>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="nav-link text-slate-300 hover:text-white px-3 py-2">Home</a>
                    <a href="#about" class="nav-link text-slate-300 hover:text-white px-3 py-2">About</a>
                    <a href="#services" class="nav-link text-slate-300 hover:text-white px-3 py-2">Services</a>
                    <a href="#portfolio" class="nav-link text-slate-300 hover:text-white px-3 py-2">Portfolio</a>
                    <a href="#contact" class="nav-link text-slate-300 hover:text-white px-3 py-2">Contact</a>
                    @auth<a href="{{ route('dashboard') }}" class="nav-link text-purple-400 px-3 py-2">Dashboard</a>@else<a href="{{ route('login') }}" class="nav-link text-purple-400 px-3 py-2">Login</a>@endauth
                </div>
                <button id="mobile-menu-button" class="md:hidden text-slate-300"><i class="fas fa-bars text-xl"></i></button>
            </div>
            <div id="mobile-menu" class="mobile-menu md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#home" class="block text-slate-300 hover:bg-slate-800 px-3 py-2 rounded">Home</a>
                    <a href="#about" class="block text-slate-300 hover:bg-slate-800 px-3 py-2 rounded">About</a>
                    <a href="#services" class="block text-slate-300 hover:bg-slate-800 px-3 py-2 rounded">Services</a>
                    <a href="#portfolio" class="block text-slate-300 hover:bg-slate-800 px-3 py-2 rounded">Portfolio</a>
                    <a href="#contact" class="block text-slate-300 hover:bg-slate-800 px-3 py-2 rounded">Contact</a>
                    @auth<a href="{{ route('dashboard') }}" class="block text-purple-400 hover:bg-slate-800 px-3 py-2 rounded">Dashboard</a>@else<a href="{{ route('login') }}" class="block text-purple-400 hover:bg-slate-800 px-3 py-2 rounded">Login</a>@endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="home" class="pt-24 pb-20 px-4 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold mb-6"><span class="text-white">Asynchronous</span> <span class="text-purple-500">Digital</span></h1>
            <p class="text-xl sm:text-2xl text-slate-300 mb-8 max-w-3xl mx-auto">A dedicated cloud team specializing in modern software development. We bring your digital visions to life with precision and efficiency.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                <a href="#contact" class="px-8 py-4 bg-purple-600 text-white rounded-full font-semibold hover:bg-purple-700 transition transform hover:scale-105">Get Started <i class="fas fa-arrow-right ml-2"></i></a>
                <a href="#portfolio" class="px-8 py-4 bg-slate-800 text-white rounded-full font-semibold hover:bg-slate-700 transition">View Our Work</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 max-w-5xl mx-auto">
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fab fa-android text-4xl text-purple-500 mb-2"></i><span class="text-sm text-slate-300">Android</span></div>
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fas fa-mobile-alt text-4xl text-blue-500 mb-2"></i><span class="text-sm text-slate-300">Flutter</span></div>
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fas fa-code text-4xl text-green-500 mb-2"></i><span class="text-sm text-slate-300">Web Apps</span></div>
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fas fa-globe text-4xl text-yellow-500 mb-2"></i><span class="text-sm text-slate-300">Websites</span></div>
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fas fa-server text-4xl text-red-500 mb-2"></i><span class="text-sm text-slate-300">DevOps</span></div>
                <div class="flex flex-col items-center p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition"><i class="fas fa-pen-nib text-4xl text-cyan-500 mb-2"></i><span class="text-sm text-slate-300">Design</span></div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-20 px-4 bg-slate-800/30">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">About <span class="text-purple-500">Us</span></h2>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto">We are a dedicated cloud team committed to delivering exceptional digital solutions that drive innovation and growth.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
                <div class="text-center"><div class="text-5xl font-bold text-purple-500 mb-2">{{ $stats['projects_completed'] }}+</div><div class="text-slate-400">Projects Completed</div></div>
                <div class="text-center"><div class="text-5xl font-bold text-purple-500 mb-2">{{ $stats['active_clients'] }}+</div><div class="text-slate-400">Active Clients</div></div>
                <div class="text-center"><div class="text-5xl font-bold text-purple-500 mb-2">{{ $stats['team_members'] }}+</div><div class="text-slate-400">Team Members</div></div>
                <div class="text-center"><div class="text-5xl font-bold text-purple-500 mb-2">{{ $stats['years_experience'] }}+</div><div class="text-slate-400">Years Experience</div></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="bg-slate-800/50 p-8 rounded-xl border border-slate-700 text-center"><i class="fas fa-lightbulb text-5xl text-purple-500 mb-4"></i><h3 class="text-2xl font-bold mb-3">Innovation</h3><p class="text-slate-400">We constantly explore new technologies to deliver cutting-edge solutions.</p></div>
                <div class="bg-slate-800/50 p-8 rounded-xl border border-slate-700 text-center"><i class="fas fa-award text-5xl text-purple-500 mb-4"></i><h3 class="text-2xl font-bold mb-3">Excellence</h3><p class="text-slate-400">Quality is non-negotiable. We strive for excellence in every line of code.</p></div>
                <div class="bg-slate-800/50 p-8 rounded-xl border border-slate-700 text-center"><i class="fas fa-handshake text-5xl text-purple-500 mb-4"></i><h3 class="text-2xl font-bold mb-3">Partnership</h3><p class="text-slate-400">Your success is our success. We work as an extension of your team.</p></div>
            </div>
            @if($teamMembers->count() > 0)
            <div class="text-center mb-12"><h3 class="text-3xl font-bold">Meet Our Team</h3></div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach($teamMembers as $member)
                <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 text-center hover:border-purple-500 transition">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-4"><span class="text-2xl font-bold">{{ substr($member->name, 0, 1) }}</span></div>
                    <h4 class="text-lg font-bold mb-1">{{ $member->name }}</h4>
                    <p class="text-purple-400 text-sm">{{ ucfirst($member->role->name ?? 'Team Member') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Our <span class="text-purple-500">Services</span></h2>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto">Comprehensive software development services tailored to your business needs.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services as $service)
                <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-8 hover:border-purple-500 transition transform hover:scale-105">
                    @if($service->icon)<div class="text-5xl mb-6 text-purple-500">{!! $service->icon !!}</div>@endif
                    <h3 class="text-2xl font-bold mb-3">{{ $service->title }}</h3>
                    <p class="text-slate-400 mb-6">{{ $service->short_description }}</p>
                    @if($service->features && count($service->features) > 0)
                    <ul class="space-y-2 mb-6">
                        @foreach(array_slice($service->features, 0, 4) as $feature)
                        <li class="flex items-start text-slate-300 text-sm"><i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i><span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>
                    @endif
                    @if($service->price_display)
                    <div class="mt-6 pt-6 border-t border-slate-700"><p class="text-lg font-semibold text-purple-400">{{ $service->price_display }}</p></div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-12"><p class="text-slate-400">No services available at the moment.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Portfolio -->
    <section id="portfolio" class="py-20 px-4 bg-slate-800/30">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Our <span class="text-purple-500">Portfolio</span></h2>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto">Explore our completed projects and see how we've helped businesses achieve their digital goals.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 hover:border-purple-500 transition transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-xl font-bold flex-1">{{ $project->name }}</h3>
                        <span class="ml-2 px-3 py-1 text-xs rounded-full bg-green-600/20 text-green-400">{{ ucfirst($project->status) }}</span>
                    </div>
                    @if($project->client)<div class="flex items-center mb-4 text-sm text-slate-400"><i class="fas fa-building mr-2"></i>{{ $project->client->company_name }}</div>@endif
                    <p class="text-slate-400 text-sm mb-4">{{ Str::limit($project->description, 100) ?: 'No description available.' }}</p>
                    @if($project->tech_stack)
                    <div class="flex flex-wrap gap-2">
                        @foreach(array_slice(explode(',', $project->tech_stack), 0, 3) as $tech)
                        <span class="px-2 py-1 bg-slate-700 text-slate-300 text-xs rounded">{{ trim($tech) }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-12"><i class="fas fa-folder-open text-6xl text-slate-700 mb-4"></i><p class="text-slate-400">Our portfolio projects will be displayed here soon.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if($featuredTestimonials->count() > 0)
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16"><h2 class="text-4xl md:text-5xl font-bold mb-6">What Our <span class="text-purple-500">Clients Say</span></h2></div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredTestimonials as $testimonial)
                <div class="bg-slate-800/50 p-6 rounded-xl border border-slate-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center mr-3"><span class="font-bold">{{ substr($testimonial->client_name, 0, 1) }}</span></div>
                        <div><h4 class="font-semibold">{{ $testimonial->client_name }}</h4>@if($testimonial->client_company)<p class="text-sm text-slate-400">{{ $testimonial->client_company }}</p>@endif</div>
                    </div>
                    <div class="flex mb-3">@for($i = 1; $i <= 5; $i++)<i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-slate-600' }}"></i>@endfor</div>
                    <p class="text-slate-300 text-sm">{{ $testimonial->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Contact -->
    <section id="contact" class="py-20 px-4 bg-slate-800/30">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Get In <span class="text-purple-500">Touch</span></h2>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto">Have a project in mind? Let's discuss how we can help bring your ideas to life.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-8">
                        @if(session('success'))<div class="mb-6 p-4 bg-green-600/20 border border-green-600 rounded-lg text-green-400"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
                        @if(session('error'))<div class="mb-6 p-4 bg-red-600/20 border border-red-600 rounded-lg text-red-400"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Name *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">@error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror</div>
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Email *</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">@error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror</div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Phone</label><input type="tel" name="phone" value="{{ old('phone') }}" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"></div>
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Company</label><input type="text" name="company" value="{{ old('company') }}" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"></div>
                            </div>
                            <div class="mb-6"><label class="block text-sm font-medium text-slate-300 mb-2">Subject *</label><input type="text" name="subject" value="{{ old('subject') }}" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">@error('subject')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Service Interest</label><select name="service_interest" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"><option value="">Select a service...</option>@foreach($services as $service)<option value="{{ $service->title }}">{{ $service->title }}</option>@endforeach</select></div>
                                <div><label class="block text-sm font-medium text-slate-300 mb-2">Budget Range</label><select name="budget_range" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"><option value="">Select budget...</option><option value="Under $5,000">Under $5,000</option><option value="$5,000 - $10,000">$5,000 - $10,000</option><option value="$10,000 - $25,000">$10,000 - $25,000</option><option value="$25,000 - $50,000">$25,000 - $50,000</option><option value="$50,000+">$50,000+</option></select></div>
                            </div>
                            <div class="mb-6"><label class="block text-sm font-medium text-slate-300 mb-2">Message *</label><textarea name="message" rows="5" required class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">{{ old('message') }}</textarea>@error('message')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror</div>
                            <button type="submit" class="w-full px-8 py-4 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition"><i class="fas fa-paper-plane mr-2"></i>Send Message</button>
                        </form>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-xl font-bold mb-6">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start"><div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-envelope text-purple-500 text-xl"></i></div><div><h4 class="font-semibold mb-1">Email</h4><a href="mailto:opqclick@gmail.com" class="text-slate-400 hover:text-purple-500">opqclick@gmail.com</a></div></div>
                            <div class="flex items-start"><div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-clock text-purple-500 text-xl"></i></div><div><h4 class="font-semibold mb-1">Working Hours</h4><p class="text-slate-400">24/7 Support Available</p></div></div>
                            <div class="flex items-start"><div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-map-marker-alt text-purple-500 text-xl"></i></div><div><h4 class="font-semibold mb-1">Location</h4><p class="text-slate-400">Remote / Cloud-based</p></div></div>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition"><i class="fab fa-twitter text-xl"></i></a>
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition"><i class="fab fa-linkedin text-xl"></i></a>
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition"><i class="fab fa-github text-xl"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-800/50 border-t border-slate-700">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2"><h3 class="text-2xl font-bold mb-3"><span class="text-purple-500">Async</span><span>Digital</span></h3><p class="text-slate-400 mb-4">A dedicated cloud team specializing in modern software development.</p></div>
                <div><h4 class="font-semibold mb-3">Quick Links</h4><ul class="space-y-2"><li><a href="#home" class="text-slate-400 hover:text-purple-500">Home</a></li><li><a href="#about" class="text-slate-400 hover:text-purple-500">About</a></li><li><a href="#services" class="text-slate-400 hover:text-purple-500">Services</a></li><li><a href="#portfolio" class="text-slate-400 hover:text-purple-500">Portfolio</a></li></ul></div>
                <div><h4 class="font-semibold mb-3">Contact</h4><ul class="space-y-2 text-slate-400"><li><a href="#contact" class="hover:text-purple-500">Get In Touch</a></li><li><a href="mailto:opqclick@gmail.com" class="hover:text-purple-500">opqclick@gmail.com</a></li>@auth<li><a href="{{ route('dashboard') }}" class="hover:text-purple-500">Dashboard</a></li>@endauth</ul></div>
            </div>
            <div class="border-t border-slate-700 pt-6 text-center"><p class="text-sm text-slate-400">© {{ date('Y') }} Asynchronous Digital. All rights reserved.</p></div>
        </div>
    </footer>

    <script>
        window.addEventListener('load', () => { const l = document.getElementById('loader'); setTimeout(() => { l.style.opacity = '0'; setTimeout(() => l.remove(), 500); }, 1000); });
        document.getElementById('mobile-menu-button')?.addEventListener('click', () => document.getElementById('mobile-menu')?.classList.toggle('active'));
        document.querySelectorAll('#mobile-menu a').forEach(l => l.addEventListener('click', () => document.getElementById('mobile-menu')?.classList.remove('active')));
        const sections = document.querySelectorAll('section[id]'), navLinks = document.querySelectorAll('.nav-link');
        window.addEventListener('scroll', () => { let current = ''; sections.forEach(s => { if (scrollY >= s.offsetTop - 200) current = s.id; }); navLinks.forEach(l => { l.classList.remove('active'); if (l.getAttribute('href') === `#${current}`) l.classList.add('active'); }); });
    </script>
</body>
</html>
