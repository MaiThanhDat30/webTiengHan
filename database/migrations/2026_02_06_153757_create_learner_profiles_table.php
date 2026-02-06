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
        Schema::create('learner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
        
            $table->unsignedTinyInteger('current_topik'); // trình độ hiện tại
            $table->unsignedTinyInteger('target_topik');  // mục tiêu
            $table->unsignedTinyInteger('daily_new_words')->default(10);
        
            $table->float('memory_rate')->default(0); // % nhớ từ
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_profiles');
    }
};
