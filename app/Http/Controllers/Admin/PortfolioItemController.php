<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PortfolioItemController extends Controller
{
    public function index()
    {
        $portfolioItems = PortfolioItem::withTrashed()->orderBy('display_order')->get();

        return view('admin.portfolio-items.index', compact('portfolioItems'));
    }

    public function create()
    {
        return view('admin.portfolio-items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'image_file' => 'nullable|image|max:2048',
            'project_url' => 'nullable|url|max:500',
            'tech_tags' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_published'] = $request->has('is_published');
        $validated['tech_tags'] = $this->parseTechTags($request->input('tech_tags'));

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('portfolio', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        PortfolioItem::create($validated);

        return redirect()->route('admin.portfolio-items.index')
            ->with('success', 'Portfolio item created successfully.');
    }

    public function show(PortfolioItem $portfolioItem)
    {
        return view('admin.portfolio-items.show', compact('portfolioItem'));
    }

    public function edit(PortfolioItem $portfolioItem)
    {
        return view('admin.portfolio-items.edit', compact('portfolioItem'));
    }

    public function update(Request $request, PortfolioItem $portfolioItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500', // Still allow URLs
            'image_file' => 'nullable|image|max:2048', // Add file upload support
            'project_url' => 'nullable|url|max:500',
            'tech_tags' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? $portfolioItem->display_order;
        $validated['is_published'] = $request->has('is_published');
        $validated['tech_tags'] = $this->parseTechTags($request->input('tech_tags'));

        if ($request->hasFile('image_file')) {
            // Delete old image if it was uploaded
            if ($portfolioItem->image_url && str_starts_with($portfolioItem->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $portfolioItem->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('portfolio', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $portfolioItem->update($validated);

        return redirect()->route('admin.portfolio-items.index')
            ->with('success', 'Portfolio item updated successfully.');
    }

    public function destroy(Request $request, PortfolioItem $portfolioItem)
    {
        $forceDelete = $request->input('delete_mode') === 'force';

        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            try {
                $portfolioItem->forceDelete();

                return redirect()->route('admin.portfolio-items.index')
                    ->with('success', 'Portfolio item permanently deleted.');
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->route('admin.portfolio-items.index')
                    ->with('error', 'Permanent delete blocked due to dependent data.');
            }
        }

        $portfolioItem->delete();

        return redirect()->route('admin.portfolio-items.index')
            ->with('success', 'Portfolio item deleted successfully.');
    }

    private function parseTechTags(?string $raw): ?array
    {
        if (empty($raw)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
