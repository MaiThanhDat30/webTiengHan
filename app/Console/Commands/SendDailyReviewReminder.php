<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserVocabProgress;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\DailyReviewReminderMail;

class SendDailyReviewReminder extends Command
{
    /**
     * Tên command chạy bằng artisan
     */
    protected $signature = 'review:daily-reminder';

    /**
     * Mô tả
     */
    protected $description = 'Gửi email nhắc học từ vựng mỗi ngày';

    /**
     * Logic chính
     */
    public function handle()
    {
        $today = Carbon::today();

        // Lấy các từ đến hạn ôn
        $progressByUser = UserVocabProgress::with('vocabulary')
            ->where('next_review_at', '<=', $today)
            ->get()
            ->groupBy('user_id');

        if ($progressByUser->isEmpty()) {
            $this->info('🎉 Hôm nay không có từ nào cần ôn');
            return;
        }

        foreach ($progressByUser as $userId => $items) {
            $user = User::find($userId);
            if (!$user) continue;

            Mail::to($user->email)->send(
                new DailyReviewReminderMail($user, $items)
            );

            $this->info("📧 Đã gửi mail cho {$user->email}");
        }

        $this->info('✅ Hoàn tất gửi mail nhắc học');
    }
}
