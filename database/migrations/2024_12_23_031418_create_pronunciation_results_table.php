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
        Schema::create('pronunciation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->comment('làm bài');
            $table->unsignedInteger('pronunciation_detail_id')->comment('pronunciation_details.id');
            $table->text('content')->nullable()->comment('Nội dung phát âm');
            $table->text('audio')->nullable()->comment('audio ghi âm');
            $table->unsignedInteger('point')->nullable()->comment('điểm số');
            $table->float('rate')->nullable()->comment('đánh giá');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciation_results');
    }
};
