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
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('topic_id')
                  ->constrained()
                  ->cascadeOnDelete();
        
            $table->string('word_kr');
            $table->string('word_vi');
        
            $table->tinyInteger('topik_level')->unsigned(); // TOPIK 1–6
            $table->tinyInteger('difficulty')->unsigned()->default(1); // 1–5
        
            $table->text('example')->nullable();
        
            $table->string('category', 50)->default('giao_tiep');

        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabularies');
    }
};
