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
        Schema::create('pronunciations', function (Blueprint $table) {
            $table->id();
            $table->string('topic_name')->nullable()->comment('Tên chủ đề');
            $table->unsignedInteger('user_id')->comment('người tạo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciations');
    }
};
