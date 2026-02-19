<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonial::withTrashed()->with([
            'client' => fn ($query) => $query->withTrashed(),
            'project' => fn ($query) => $query->withTrashed(),
        ])->orderBy('order')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::with('user')->get();
        $projects = Project::with('client.user')->get();
        return view('admin.testimonials.create', compact('clients', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_avatar' => 'nullable|url|max:500',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        $validated['order'] = $validated['order'] ?? 0;

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        $testimonial->load(['client.user', 'project']);
        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        $clients = Client::with('user')->get();
        $projects = Project::with('client.user')->get();
        return view('admin.testimonials.edit', compact('testimonial', 'clients', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_avatar' => 'nullable|url|max:500',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        $validated['order'] = $validated['order'] ?? $testimonial->order;

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Testimonial $testimonial)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            try {
                $testimonial->forceDelete();

                return redirect()->route('admin.testimonials.index')
                    ->with('success', 'Testimonial permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.testimonials.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
}
