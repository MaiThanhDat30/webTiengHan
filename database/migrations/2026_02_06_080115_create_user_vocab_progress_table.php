<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_vocab_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vocabulary_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('repetition')->default(0);
            $table->integer('interval')->default(1);
            $table->date('next_review_at');

            $table->timestamps();

            $table->unique(['user_id', 'vocabulary_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vocab_progress');
    }
};
