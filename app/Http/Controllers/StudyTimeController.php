<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudyTimeController extends Controller
{
    public function ping(Request $request)
    {
        $userId = auth()->id();
        $today  = now()->toDateString();

        $study = StudyTime::firstOrCreate(
            [
                'user_id' => $userId,
                'date'    => $today,
            ],
            [
                'minutes'      => 0,
                'last_ping_at' => now(),
            ]
        );

        // chỉ cộng phút nếu user còn active (<= 5 phút)
        if (
            $study->last_ping_at &&
            now()->diffInMinutes($study->last_ping_at) <= 5
        ) {
            $study->increment('minutes');
        }

        $study->update([
            'last_ping_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
