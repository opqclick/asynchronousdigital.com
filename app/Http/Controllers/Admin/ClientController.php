<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with(['user', 'projects'])->get();
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'client_email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Get client role
        $clientRole = Role::where('name', 'client')->first();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $clientRole->id,
        ]);

        // Create client profile
        Client::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['client_email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['user', 'projects.tasks', 'invoices']);
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $client->load('user');
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($client->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'client_email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Update user
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $client->user()->update($userData);

        // Update client profile
        $client->update([
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['client_email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Delete associated user
        $user = $client->user;
        $client->delete();
        $user->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
