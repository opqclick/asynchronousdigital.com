<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display a listing of the team member's salaries.
     */
    public function index(Request $request)
    {
        $filterMonth = $request->input('month', now()->format('Y-m'));
        
        $salaries = Salary::with(['project'])
            ->where('user_id', auth()->id())
            ->whereYear('month', '=', substr($filterMonth, 0, 4))
            ->whereMonth('month', '=', substr($filterMonth, 5, 2))
            ->orderBy('month', 'desc')
            ->get();
            
        return view('team-member.salaries.index', compact('salaries', 'filterMonth'));
    }

    /**
     * Display the specified salary.
     */
    public function show(Salary $salary)
    {
        // Ensure team member can only view their own salary
        if ($salary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('team-member.salaries.show', compact('salary'));
    }

    /**
     * Share salary slip via email or WhatsApp
     */
    public function share(Salary $salary)
    {
        // Ensure team member can only share their own salary
        if ($salary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Generate shareable URL
        $shareUrl = route('team-member.salaries.show', $salary);
        $user = auth()->user();
        
        return view('team-member.salaries.share', compact('salary', 'shareUrl', 'user'));
    }

    /**
     * Confirm salary received by team member
     */
    public function confirmReceived(Salary $salary)
    {
        // Ensure team member can only confirm their own salary
        if ($salary->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if salary is paid
        if ($salary->status !== 'paid') {
            return redirect()->back()->with('error', 'Cannot confirm receipt for unpaid salary.');
        }

        // Check if already confirmed
        if ($salary->is_received) {
            return redirect()->back()->with('info', 'Salary receipt already confirmed.');
        }

        // Confirm receipt
        $salary->update([
            'is_received' => true,
            'received_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Salary receipt confirmed successfully!');
    }
}
