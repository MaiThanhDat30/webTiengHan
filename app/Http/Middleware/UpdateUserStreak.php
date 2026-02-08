<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserStreak;

class UpdateUserStreak
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ xử lý khi user đã login
        if (!Auth::check()) {
            return $next($request);
        }

        $userId = Auth::id();
        $today  = Carbon::today();

        // Lấy hoặc tạo streak cho user
        $streak = UserStreak::firstOrCreate(
            ['user_id' => $userId],
            [
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_study_date' => null,
            ]
        );

        // Nếu chưa từng học ngày nào
        if (!$streak->last_study_date) {
            $streak->current_streak = 1;
            $streak->longest_streak = max(1, $streak->longest_streak);
            $streak->last_study_date = $today;
            $streak->save();

            return $next($request);
        }

        $lastDate = Carbon::parse($streak->last_study_date);

        // Nếu đã tính streak hôm nay rồi → bỏ qua
        if ($lastDate->isSameDay($today)) {
            return $next($request);
        }

        // Nếu hôm nay là ngày kế tiếp → cộng streak
        if ($lastDate->addDay()->isSameDay($today)) {
            $streak->current_streak += 1;
        } else {
            // Bị đứt streak → reset
            $streak->current_streak = 1;
        }

        // Cập nhật longest
        if ($streak->current_streak > $streak->longest_streak) {
            $streak->longest_streak = $streak->current_streak;
        }

        $streak->last_study_date = $today;
        $streak->save();

        return $next($request);
    }
}
