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
    protected $signature = 'review:daily-reminder';

    protected $description = 'Gửi email nhắc học từ vựng nếu có từ đến hạn';

    public function handle()
    {
        $today = Carbon::today();

        // Lấy tất cả từ đến hạn ôn (<= hôm nay)
        $progressByUser = UserVocabProgress::with('vocabulary')
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', $today)
            ->get()
            ->groupBy('user_id');

        // ❌ Không có từ → không gửi
        if ($progressByUser->isEmpty()) {
            $this->info('🎉 Không có từ nào cần ôn – không gửi mail');
            return Command::SUCCESS;
        }

        foreach ($progressByUser as $userId => $items) {

            // An toàn
            if ($items->isEmpty()) {
                continue;
            }

            $user = User::find($userId);
            if (!$user || !$user->email) {
                continue;
            }

            Mail::to($user->email)->send(
                new DailyReviewReminderMail($user, $items)
            );

            $this->info("📧 Đã gửi mail cho {$user->email}");
        }

        $this->info('✅ Hoàn tất gửi mail nhắc học');
        return Command::SUCCESS;
    }
}
