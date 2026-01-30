<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Asynchronous Digital</title>
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

    <!-- Navigation Header -->
    @include('public.partials.nav')

    <!-- Contact Hero Section -->
    <section class="pt-24 pb-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">Get In <span class="text-purple-500">Touch</span></h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Have a project in mind? Let's discuss how we can help bring your ideas to life.
            </p>
        </div>
    </section>

    <!-- Contact Form & Info Section -->
    <section class="py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-8">
                        <h2 class="text-2xl font-bold mb-6">Send Us a Message</h2>

                        <!-- Success Message -->
                        @if(session('success'))
                        <div class="mb-6 p-4 bg-green-600/20 border border-green-600 rounded-lg text-green-400">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                        @endif

                        <!-- Error Message -->
                        @if(session('error'))
                        <div class="mb-6 p-4 bg-red-600/20 border border-red-600 rounded-lg text-red-400">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                                        Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500 @error('email') border-red-500 @enderror">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">
                                        Phone
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">
                                </div>

                                <!-- Company -->
                                <div>
                                    <label for="company" class="block text-sm font-medium text-slate-300 mb-2">
                                        Company
                                    </label>
                                    <input type="text" id="company" name="company" value="{{ old('company') }}"
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="mb-6">
                                <label for="subject" class="block text-sm font-medium text-slate-300 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                                    class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500 @error('subject') border-red-500 @enderror">
                                @error('subject')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Service Interest -->
                                <div>
                                    <label for="service_interest" class="block text-sm font-medium text-slate-300 mb-2">
                                        Service Interest
                                    </label>
                                    <select id="service_interest" name="service_interest"
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">
                                        <option value="">Select a service...</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->title }}" {{ old('service_interest') == $service->title ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Budget Range -->
                                <div>
                                    <label for="budget_range" class="block text-sm font-medium text-slate-300 mb-2">
                                        Budget Range
                                    </label>
                                    <select id="budget_range" name="budget_range"
                                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500">
                                        <option value="">Select budget range...</option>
                                        <option value="Under $5,000" {{ old('budget_range') == 'Under $5,000' ? 'selected' : '' }}>Under $5,000</option>
                                        <option value="$5,000 - $10,000" {{ old('budget_range') == '$5,000 - $10,000' ? 'selected' : '' }}>$5,000 - $10,000</option>
                                        <option value="$10,000 - $25,000" {{ old('budget_range') == '$10,000 - $25,000' ? 'selected' : '' }}>$10,000 - $25,000</option>
                                        <option value="$25,000 - $50,000" {{ old('budget_range') == '$25,000 - $50,000' ? 'selected' : '' }}>$25,000 - $50,000</option>
                                        <option value="$50,000+" {{ old('budget_range') == '$50,000+' ? 'selected' : '' }}>$50,000+</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="mb-6">
                                <label for="message" class="block text-sm font-medium text-slate-300 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full px-8 py-4 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-300">
                                <i class="fas fa-paper-plane mr-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact Info & Map -->
                <div class="space-y-6">
                    <!-- Contact Cards -->
                    <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                        <h3 class="text-xl font-bold mb-6">Contact Information</h3>

                        <div class="space-y-6">
                            <!-- Email -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-envelope text-purple-500 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-1">Email</h4>
                                    <a href="mailto:opqclick@gmail.com" class="text-slate-400 hover:text-purple-500 transition-colors">
                                        opqclick@gmail.com
                                    </a>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-phone text-purple-500 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-1">Phone</h4>
                                    <p class="text-slate-400">Available on request</p>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-map-marker-alt text-purple-500 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-1">Location</h4>
                                    <p class="text-slate-400">Remote / Cloud-based</p>
                                </div>
                            </div>

                            <!-- Working Hours -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-clock text-purple-500 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-1">Working Hours</h4>
                                    <p class="text-slate-400">24/7 Support Available</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                        <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition-colors">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition-colors">
                                <i class="fab fa-github text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center hover:bg-purple-600 transition-colors">
                                <i class="fab fa-facebook text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                        <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                        <div class="space-y-3">
                            <a href="{{ route('services') }}" class="block text-slate-400 hover:text-purple-500 transition-colors">
                                <i class="fas fa-arrow-right mr-2 text-sm"></i>View Our Services
                            </a>
                            <a href="{{ route('portfolio') }}" class="block text-slate-400 hover:text-purple-500 transition-colors">
                                <i class="fas fa-arrow-right mr-2 text-sm"></i>Browse Portfolio
                            </a>
                            <a href="{{ route('about') }}" class="block text-slate-400 hover:text-purple-500 transition-colors">
                                <i class="fas fa-arrow-right mr-2 text-sm"></i>About Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 px-4 bg-slate-800/30">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold mb-2">What is your typical project timeline?</h3>
                    <p class="text-slate-400">Project timelines vary based on scope and complexity. Small projects typically take 2-4 weeks, while larger applications may take 2-6 months. We'll provide a detailed timeline during our initial consultation.</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold mb-2">Do you offer ongoing support and maintenance?</h3>
                    <p class="text-slate-400">Yes! We provide comprehensive post-launch support and maintenance packages to ensure your application stays up-to-date, secure, and running smoothly.</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold mb-2">How do you handle project communication?</h3>
                    <p class="text-slate-400">We believe in transparent, regular communication. We use project management tools, schedule regular check-ins, and provide detailed progress reports throughout the development process.</p>
                </div>
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
