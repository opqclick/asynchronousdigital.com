<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\TeamContent;
use App\Models\Testimonial;
use App\Models\PortfolioItem;

class PublicController extends Controller
{
    public function home()
    {
        // Get all data for single-page website
        $services = Service::active()->get();
        $featuredTestimonials = Testimonial::featured()->take(6)->get();
        $projects = Project::where('status', 'completed')
            ->with(['client', 'teams'])
            ->latest()
            ->take(9)
            ->get();

        // Team content for about section
        $teamMembers = TeamContent::published()->get();

        // Statistics for about section
        $stats = [
            'projects_completed' => Project::where('status', 'completed')->count(),
            'active_clients' => Project::whereIn('status', ['active', 'completed'])->distinct('client_id')->count('client_id'),
            'team_members' => $teamMembers->count(),
            'years_experience' => 5,
        ];

        // Get unique technologies from all projects
        $technologies = Project::pluck('tech_stack')
            ->flatMap(function ($tech) {
                // tech_stack is already an array, not a string
                return is_array($tech) ? $tech : [];
            })
            ->map(fn($t) => trim($t))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('welcome', compact('services', 'featuredTestimonials', 'projects', 'teamMembers', 'stats', 'technologies'));
    }

    public function about()
    {
        $teamMembers = TeamContent::published()->get();

        $stats = [
            'projects_completed' => Project::where('status', 'completed')->count(),
            'active_clients' => Project::whereIn('status', ['active', 'completed'])->distinct('client_id')->count('client_id'),
            'team_members' => $teamMembers->count(),
            'years_experience' => 5, // You can make this dynamic
        ];

        return view('public.about', compact('teamMembers', 'stats'));
    }

    public function services()
    {
        $services = Service::active()->get();
        return view('public.services', compact('services'));
    }

    public function portfolio()
    {
        $query = Project::with(['client', 'teams']);

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by technology
        if (request('tech')) {
            $query->where('tech_stack', 'like', '%' . request('tech') . '%');
        }

        // Search
        if (request('search')) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('description', 'like', '%' . request('search') . '%');
            });
        }

        $projects = $query->latest()->paginate(12);

        // Get curated portfolio items
        $portfolioItems = PortfolioItem::published()->get();

        // Get unique technologies from all projects
        $technologies = Project::pluck('tech_stack')
            ->flatMap(function ($tech) {
                // tech_stack is already an array, not a string
                return is_array($tech) ? $tech : [];
            })
            ->map(fn($t) => trim($t))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('public.portfolio', compact('projects', 'technologies', 'portfolioItems'));
    }

    public function contact()
    {
        $services = Service::active()->get();
        return view('public.contact', compact('services'));
    }
}
