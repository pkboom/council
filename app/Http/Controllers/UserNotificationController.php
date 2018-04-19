<?php

namespace App\Http\Controllers;

class UserNotificationController extends Controller
{
    public function index()
    {
        return auth()->user()->unreadNotifications;
    }

    public function destroy($user, $notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);

        $notification->markAsRead();

        return json_encode(
            $notification->data
        );
    }
}
