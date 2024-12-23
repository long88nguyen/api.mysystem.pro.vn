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
        Schema::create('pronunciation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pronunciation_id')->comment('pronunciations.id');
            $table->text('content')->nullable()->comment('ký tự, từ, câu cần phát âm');
            $table->text('audio')->nullable()->comment('Đường dẫn audio');
            $table->text('ipa')->nullable()->comment('Phiên âm IPA');
            $table->unsignedInteger('created_by')->comment('người tạo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciation_details');
    }
};
