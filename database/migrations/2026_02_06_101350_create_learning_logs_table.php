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
        Schema::create('learning_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('action', ['learn', 'review']);
            $table->enum('result', ['correct', 'wrong'])->nullable();

            $table->integer('interval')->nullable(); // SRS interval

            $table->timestamps(); // created_at = thời điểm học
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_logs');
    }
};
