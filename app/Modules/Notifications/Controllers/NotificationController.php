<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Notifications\Controllers;

use Exception;
use FI\Http\Controllers\Controller;
use FI\Modules\Notifications\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markViewed(Request $request, Notification $notification)
    {
        $notification->is_viewed = 1;
        $notification->viewed_at = now();
        $notification->save();
        return response()->json(['success' => true], 200);
    }

    public function markAllViewed()
    {
        try
        {
            Notification::query()->userId(auth()->user()->id)->update(['is_viewed' => 1]);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => trans('fi.notification_clear_all')], 200);
    }

    public function getNotification()
    {
        $notifications = Notification::select('*')
            ->with('notifiable')
            ->userId(auth()->user()->id)
            ->where('is_viewed', 0)
            ->sortable(['created_at' => 'desc'])
            ->get();

        return view('layouts._notification')->with('notifications', $notifications)->render();
    }

}
