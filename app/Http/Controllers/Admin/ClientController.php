<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Mail\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::withTrashed()
            ->with([
                'user' => fn ($query) => $query->withTrashed(),
                'projects' => fn ($query) => $query->withTrashed(),
            ])
            ->get();
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
            'send_invitation_email' => 'boolean',
        ]);

        // Store the plain password for the invitation email
        $plainPassword = $validated['password'];

        // Get client role
        $clientRole = Role::where('name', 'client')->first();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $clientRole->id,
            'active_role_id' => $clientRole->id,
        ]);

        $user->syncRolesWithRules([$clientRole->id], $clientRole->id);

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

        // Send invitation email if checkbox is checked
        if ($request->has('send_invitation_email')) {
            try {
                Mail::to($user->email)->send(new UserInvitation($user, $plainPassword));
            } catch (\Exception $e) {
                // Log the error but don't fail the client creation
                Log::error('Failed to send invitation email: ' . $e->getMessage());
            }
        }

        $successMessage = $request->has('send_invitation_email') 
            ? 'Client created successfully and invitation email sent.' 
            : 'Client created successfully.';

        return redirect()->route('admin.clients.index')
            ->with('success', $successMessage);
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
    public function destroy(Request $request, Client $client)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        $user = $client->user;

        if ($forceDelete) {
            $dependencies = $this->collectClientDependencies($client);
            $activeDependencies = array_filter($dependencies, fn (int $count) => $count > 0);

            if (!empty($activeDependencies)) {
                $dependencySummary = collect($activeDependencies)
                    ->map(fn (int $count, string $key) => ucfirst(str_replace('_', ' ', $key)) . ': ' . $count)
                    ->implode(', ');

                return redirect()->route('admin.clients.index')
                    ->with('error', 'Permanent delete blocked. This client has dependent data. ' . $dependencySummary . '. Please use soft delete.');
            }

            try {
                $client->forceDelete();

                if ($user && !$user->trashed()) {
                    $user->delete();
                }

                return redirect()->route('admin.clients.index')
                    ->with('success', 'Client permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.clients.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $client->delete();
        if ($user && !$user->trashed()) {
            $user->delete();
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    private function collectClientDependencies(Client $client): array
    {
        return [
            'projects' => $client->projects()->withTrashed()->count(),
            'invoices' => $client->invoices()->withTrashed()->count(),
            'payments' => Payment::withTrashed()
                ->whereHas('invoice', function ($query) use ($client) {
                    $query->withTrashed()->where('client_id', $client->id);
                })->count(),
        ];
    }

    /**
     * Send invitation email to client.
     */
    public function sendInvitation(Client $client)
    {
        // Generate a temporary password
        $temporaryPassword = Str::random(12);
        
        // Update user's password
        $client->user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        // Send invitation email
        try {
            Mail::to($client->user->email)->send(new UserInvitation($client->user, $temporaryPassword));
            return redirect()->route('admin.clients.index')
                ->with('success', 'Invitation email sent successfully to ' . $client->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send invitation email: ' . $e->getMessage());
            return redirect()->route('admin.clients.index')
                ->with('error', 'Failed to send invitation email. Please try again.');
        }
    }
}
