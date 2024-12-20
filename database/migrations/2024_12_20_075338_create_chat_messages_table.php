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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Người dùng');
            $table->unsignedBigInteger('chat_room_id')->comment('id phòng chat');
            $table->string('role')->comment('vai trò: system(hệ thống - bot chat), user(người dùng)');
            $table->text('content')->nullable()->comment('Nội dung chat');
            $table->text('audio')->nullable()->comment('audio');
            $table->text('translation')->nullable()->comment('dịch thuật');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
