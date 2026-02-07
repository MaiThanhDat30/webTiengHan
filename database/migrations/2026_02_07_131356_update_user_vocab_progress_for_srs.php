<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            // Thêm step
            $table->integer('step')->default(0)->after('vocabulary_id');

            // Xóa logic cũ
            $table->dropColumn(['repetition', 'interval']);
        });
    }

    public function down(): void
    {
        Schema::table('user_vocab_progress', function (Blueprint $table) {
            $table->integer('repetition')->default(0);
            $table->integer('interval')->default(1);
            $table->dropColumn('step');
        });
    }
};
