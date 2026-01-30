<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client as ClientModel;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = $user->client()->first();

        if (!$client) {
            return redirect()->route('login')->with('error', 'No client profile found.');
        }

        // Get all projects for this client
        $projects = $client->projects()->with('tasks')->get();

        // Get invoices
        $invoices = $client->invoices()->latest()->get();

        // Client statistics
        $stats = [
            'active_projects' => $projects->where('status', 'active')->count(),
            'total_projects' => $projects->count(),
            'pending_invoices' => $invoices->where('status', 'sent')->sum('total_amount'),
            'paid_invoices' => $invoices->where('status', 'paid')->sum('total_amount'),
        ];

        return view('client.dashboard', compact('projects', 'invoices', 'stats'));
    }
}
