<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            // 1. Drop foreign key trước
            $table->dropForeign(['topik_id']);

            // 2. Drop column
            $table->dropColumn('topik_id');
        });
    }

    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->unsignedBigInteger('topik_id')->nullable();

            // nếu trước đây có FK thì add lại
            $table->foreign('topik_id')
                  ->references('id')
                  ->on('topics')
                  ->onDelete('cascade');
        });
    }
};
