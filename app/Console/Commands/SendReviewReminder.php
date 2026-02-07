<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewReminderMail;
use Illuminate\Support\Facades\DB;

class SendReviewReminder extends Command
{
    protected $signature = 'reminder:review';
    protected $description = 'Send personalized vocabulary review emails';

    public function handle()
    {
        $users = User::whereNotNull('email_verified_at')->get();

        foreach ($users as $user) {

            // 🔔 Số từ đến hạn ôn
            $count = DB::table('learning_logs')
                ->where('user_id', $user->id)
                ->where('next_review_at', '<=', now())
                ->count();

            if ($count === 0) continue;

            // 🧠 Top từ sai nhiều nhất
            $vocabs = DB::table('learning_logs')
                ->join('vocabularies', 'learning_logs.vocabulary_id', '=', 'vocabularies.id')
                ->where('learning_logs.user_id', $user->id)
                ->where('learning_logs.result', 'wrong')
                ->select(
                    'vocabularies.word_kr',
                    DB::raw('COUNT(*) as wrongs')
                )
                ->groupBy('vocabularies.word_kr')
                ->orderByDesc('wrongs')
                ->limit(5)
                ->get();

            Mail::to($user->email)->send(
                new ReviewReminderMail($user, $count, $vocabs)
            );

            $this->info("📧 Sent reminder to {$user->email}");
        }

        return Command::SUCCESS;
    }
}
