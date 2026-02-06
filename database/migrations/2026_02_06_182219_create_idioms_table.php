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
        Schema::create('idioms', function (Blueprint $table) {
            $table->id();
            $table->string('sentence_kr');
            $table->string('sentence_vi');
            $table->string('level')->nullable(); // TOPIK I / II
            $table->string('tag')->nullable();   // quán dụng / mẫu câu
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idioms');
    }
};
