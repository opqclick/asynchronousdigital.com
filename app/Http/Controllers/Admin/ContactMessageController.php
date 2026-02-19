<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = ContactMessage::withTrashed()->with('assignedUser')->latest()->get();
        $unreadCount = ContactMessage::unread()->count();
        $newCount = ContactMessage::new()->count();
        
        return view('admin.contact-messages.index', compact('messages', 'unreadCount', 'newCount'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read
        if (!$contactMessage->read_at) {
            $contactMessage->markAsRead();
        }
        
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'team_member']);
        })->get();
        
        return view('admin.contact-messages.show', compact('contactMessage', 'users'));
    }

    /**
     * Update the specified resource (for status and assignment changes).
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,archived',
            'assigned_to' => 'nullable|exists:users,id',
            'internal_notes' => 'nullable|string',
        ]);

        $contactMessage->update($validated);

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', 'Contact message updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ContactMessage $contactMessage)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            try {
                $contactMessage->forceDelete();

                return redirect()->route('admin.contact-messages.index')
                    ->with('success', 'Contact message permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.contact-messages.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $contactMessage->delete();
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Contact message deleted successfully.');
    }
}
