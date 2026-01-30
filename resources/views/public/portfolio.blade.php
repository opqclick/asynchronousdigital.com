<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Asynchronous Digital</title>
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

    <!-- Portfolio Hero Section -->
    <section class="pt-24 pb-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">Our <span class="text-purple-500">Portfolio</span></h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Explore our completed projects and see how we've helped businesses achieve their digital goals.
            </p>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="px-4 mb-8">
        <div class="max-w-7xl mx-auto">
            <form method="GET" action="{{ route('portfolio') }}" class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Search Projects</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Project name or description..." 
                            class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                        <select name="status" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-purple-500">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>In Progress</option>
                        </select>
                    </div>

                    <!-- Technology Filter -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Technology</label>
                        <select name="tech" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-purple-500">
                            <option value="">All Technologies</option>
                            @foreach($technologies as $tech)
                            <option value="{{ $tech }}" {{ request('tech') == $tech ? 'selected' : '' }}>{{ $tech }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('portfolio') }}" class="px-6 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="py-8 px-4">
        <div class="max-w-7xl mx-auto">
            @if($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($projects as $project)
                <div class="bg-slate-800/50 backdrop-blur-lg rounded-xl border border-slate-700 overflow-hidden hover:border-purple-500 transition-all duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <!-- Project Header -->
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-bold text-white flex-1">{{ $project->name }}</h3>
                            <span class="ml-2 px-3 py-1 text-xs rounded-full
                                @if($project->status === 'completed') bg-green-600/20 text-green-400
                                @elseif($project->status === 'active') bg-blue-600/20 text-blue-400
                                @elseif($project->status === 'on_hold') bg-yellow-600/20 text-yellow-400
                                @else bg-slate-600/20 text-slate-400
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>

                        <!-- Client Info -->
                        @if($project->client)
                        <div class="flex items-center mb-4 text-sm text-slate-400">
                            <i class="fas fa-building mr-2"></i>
                            <span>{{ $project->client->company_name }}</span>
                        </div>
                        @endif

                        <!-- Description -->
                        <p class="text-slate-400 text-sm mb-4 line-clamp-3">
                            {{ $project->description ?: 'No description available.' }}
                        </p>

                        <!-- Technologies -->
                        @if($project->tech_stack)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach(array_slice(explode(',', $project->tech_stack), 0, 4) as $tech)
                            <span class="px-2 py-1 bg-slate-700 text-slate-300 text-xs rounded">{{ trim($tech) }}</span>
                            @endforeach
                            @if(count(explode(',', $project->tech_stack)) > 4)
                            <span class="px-2 py-1 bg-slate-700 text-slate-400 text-xs rounded">+{{ count(explode(',', $project->tech_stack)) - 4 }} more</span>
                            @endif
                        </div>
                        @endif

                        <!-- Teams -->
                        @if($project->teams->count() > 0)
                        <div class="flex items-center text-sm text-slate-400 mb-4">
                            <i class="fas fa-users mr-2"></i>
                            <span>{{ $project->teams->count() }} {{ Str::plural('team', $project->teams->count()) }} assigned</span>
                        </div>
                        @endif

                        <!-- Dates -->
                        <div class="flex items-center justify-between text-xs text-slate-500 pt-4 border-t border-slate-700">
                            <div>
                                <i class="far fa-calendar mr-1"></i>
                                Started: {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M Y') : 'N/A' }}
                            </div>
                            @if($project->end_date)
                            <div>
                                <i class="far fa-calendar-check mr-1"></i>
                                Completed: {{ \Carbon\Carbon::parse($project->end_date)->format('M Y') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $projects->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="text-6xl text-slate-700 mb-4">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-400 mb-2">No Projects Found</h3>
                <p class="text-slate-500">
                    @if(request('search') || request('status') || request('tech'))
                        Try adjusting your filters to see more results.
                    @else
                        Our portfolio projects will be displayed here soon.
                    @endif
                </p>
                @if(request('search') || request('status') || request('tech'))
                <a href="{{ route('portfolio') }}" class="inline-block mt-6 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
                @endif
            </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 bg-gradient-to-r from-purple-900/30 to-pink-900/30">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Want to See Your Project Here?</h2>
            <p class="text-lg text-slate-300 mb-8">
                Let's work together to create something amazing that showcases your vision.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="px-8 py-4 bg-purple-600 text-white rounded-full font-semibold hover:bg-purple-700 transition-colors duration-300">
                    Start Your Project <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('services') }}" class="px-8 py-4 bg-slate-800 text-white rounded-full font-semibold hover:bg-slate-700 transition-colors duration-300">
                    View Services
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
