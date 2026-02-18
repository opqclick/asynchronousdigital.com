<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

    public function unreadFeed(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user
            ->unreadNotifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notification) use ($user) {
                $data = is_array($notification->data) ? $notification->data : [];

                return [
                    'id' => $notification->id,
                    'message' => (string) ($data['message'] ?? 'You have a new notification.'),
                    'target_url' => $this->resolveTargetUrl($user, $data),
                    'created_at' => optional($notification->created_at)?->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
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

    private function resolveTargetUrl($user, array $payload): string
    {
        if (!empty($payload['task_id'])) {
            return method_exists($user, 'isTeamMember') && $user->isTeamMember()
                ? route('team-member.dashboard', ['open_task' => $payload['task_id']])
                : route('admin.tasks.show', $payload['task_id']);
        }

        $targetUrl = (string) ($payload['target_url'] ?? '');
        if ($targetUrl !== '') {
            return $targetUrl;
        }

        return route('notifications.index');
    }

}
