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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('bot_name')->nullable()->default('Bot chat')->comment('Tên trợ lý ảo');
            $table->string('bot_avatar')->nullable()->default('model_1')->comment('Ảnh đại diện trợ lý ảo');
            $table->text('bot_description')->nullable()->comment('Mô tả trợ lý ảo');
            $table->unsignedInteger('user_id')->comment('Người dùng');
            $table->string('name')->nullable()->comment('Tên phòng chat');
            $table->string('text_to_speech_model')->nullable()->comment('Dịch vụ chuyển đổi text thành âm thanh');
            $table->string('voice_model')->nullable()->comment('Giọng đọc của OpenAI');
            $table->string('speech_to_text_model')->nullable()->comment('Dịch vụ chuyển đổi âm thanh thành text');
            $table->string('chat_gpt_model')->nullable()->comment('Dịch vụ chat gpt');
            $table->string('language')->nullable()->comment('ngôn ngữ đầu ra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
