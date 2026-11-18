<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserNotification::where('user_id', Auth::id())->latest();

        if ($request->get('filter') === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = UserNotification::where('user_id', Auth::id())->unread()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(Request $request, int $id): RedirectResponse
    {
        $notification = UserNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        if ($request->boolean('go') && $notification->link) {
            return redirect($notification->link);
        }

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    public function markAllRead(): RedirectResponse
    {
        UserNotification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
