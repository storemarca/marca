<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('user.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح.');
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        
        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات بنجاح.');
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get latest unread notifications.
     */
    public function getLatest()
    {
        $notifications = Auth::user()->unreadNotifications()->latest()->take(5)->get();
        
        return response()->json([
            'notifications' => $notifications
        ]);
    }
} 