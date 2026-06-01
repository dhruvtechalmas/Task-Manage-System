<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function markAllAsRead(Request $request): RedirectResponse|Response
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return back();
    }

    public function destroy(Request $request, string $notification): RedirectResponse|Response
    {
        $deleted = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->delete();

        abort_if($deleted === 0, 404);

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return back();
    }
}
