<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $from_date_request = request('from_date');
        $from_date = \Carbon\Carbon::parse($from_date_request)
            ->toDateTimeString();

        $paginated_notifications = auth()->user()->notifications()
            ->when($from_date_request?true:false,
                function ($query) use ($from_date) {
                    return $query->where('created_at', '<=', $from_date);
                }
            )->latest()
            ->simplePaginate(config('consts.notifications_per_page'));

        return response()->json([
            'notifications' => $paginated_notifications
        ]);
    }


    public function unseenCount() {
        return response()->json([
            'count' => auth()->user()->notifications()
                ->where('seen', false)->count()
        ]);
    }


    public function markReadNotifications() {
        $count = auth()->user()->notifications()
            ->where('seen', false)->update(['seen' => true]);

        return response()->json(['updated' => $count]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
