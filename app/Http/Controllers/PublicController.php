<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

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

        // Team members for about section
        $teamMembers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'team_member']);
        })->get();

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
                return explode(',', $tech);
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
        $teamMembers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'team_member']);
        })->get();

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

        // Get unique technologies from all projects
        $technologies = Project::pluck('tech_stack')
            ->flatMap(function ($tech) {
                return explode(',', $tech);
            })
            ->map(fn($t) => trim($t))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('public.portfolio', compact('projects', 'technologies'));
    }

    public function contact()
    {
        $services = Service::active()->get();
        return view('public.contact', compact('services'));
    }
}
