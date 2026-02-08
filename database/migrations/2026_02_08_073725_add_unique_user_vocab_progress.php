<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'vocabulary_id'],
                'user_vocab_progress_user_vocab_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            $table->dropUnique('user_vocab_progress_user_vocab_unique');
        });
    }
};
