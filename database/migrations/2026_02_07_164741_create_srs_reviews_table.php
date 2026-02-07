<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('srs_reviews', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
    
            $table->integer('wrong_count')->default(0);
            $table->timestamp('next_review_at')->nullable();
    
            $table->timestamps();
    
            // ❗ 1 user chỉ có 1 record / 1 từ
            $table->unique(['user_id', 'vocabulary_id']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srs_reviews');
    }
};
