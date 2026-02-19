<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::withTrashed()->with([
            'invoice' => fn ($query) => $query->withTrashed()->with([
                'client' => fn ($clientQuery) => $clientQuery->withTrashed()->with([
                    'user' => fn ($userQuery) => $userQuery->withTrashed(),
                ]),
            ]),
        ])->get();
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $invoices = Invoice::with(['client.user'])
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->get();
        return view('admin.payments.create', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,credit_card,paypal,cash,check,other',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Create payment
        $payment = Payment::create($validated);

        // Update invoice paid amount
        $invoice = Invoice::find($validated['invoice_id']);
        $invoice->paid_amount += $validated['amount'];
        
        // Update status if fully paid
        if ($invoice->is_fully_paid) {
            $invoice->status = 'paid';
        }
        
        $invoice->save();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.client.user', 'invoice.project', 'invoice.payments']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Payments typically shouldn't be edited, only viewed or deleted
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Payments typically shouldn't be edited, only viewed or deleted
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Payment $payment)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        // Update invoice paid amount before deleting
        $invoice = $payment->invoice;
        $invoice->paid_amount -= $payment->amount;
        
        // Update status if no longer fully paid
        if ($invoice->status === 'paid' && !$invoice->is_fully_paid) {
            $invoice->status = 'sent';
        }
        
        $invoice->save();

        if ($forceDelete) {
            try {
                $payment->forceDelete();

                return redirect()->route('admin.payments.index')
                    ->with('success', 'Payment permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.payments.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $payment->delete();
        
        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
