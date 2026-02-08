<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();

            // User liên kết
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Streak hiện tại
            $table->unsignedInteger('current_streak')->default(0);

            // Streak cao nhất từng đạt
            $table->unsignedInteger('longest_streak')->default(0);

            // Ngày học gần nhất (chỉ lưu ngày, không lưu giờ)
            $table->date('last_study_date')->nullable();

            $table->timestamps();

            // Mỗi user chỉ có 1 streak
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_streaks');
    }
};
