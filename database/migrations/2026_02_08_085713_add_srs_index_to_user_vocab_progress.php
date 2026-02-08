<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            $table->index(
                ['user_id', 'next_review_at'],
                'idx_srs_fast'
            );
        });
    }

    public function down(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            $table->dropIndex('idx_srs_fast');
        });
    }
};