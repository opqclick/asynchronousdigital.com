<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->firstOrFail();
        $notification->markAsRead();

        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '' && $this->isSafeRedirect($redirectTo)) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index');
    }

    private function isSafeRedirect(string $url): bool
    {
        if (str_starts_with($url, '/')) {
            return true;
        }

        $appUrl = config('app.url');
        if (!is_string($appUrl) || $appUrl === '') {
            return false;
        }

        return str_starts_with($url, rtrim($appUrl, '/'));
    }
}
