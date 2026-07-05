<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companyId = session('company_id');
        $title = 'Notification Management';
        $label = 'Notifications';
        $notifications = $user->notifications()
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('data->company_id', $companyId)
                    ->orWhereNull('data->company_id');
            })
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications', 'label', 'title'));
    }


    public function markAsRead($id)
    {
        auth()->user()
            ->notifications()
            ->findOrFail($id)
            ->markAsRead();

        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}