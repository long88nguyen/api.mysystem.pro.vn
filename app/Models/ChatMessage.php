<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = "chat_messages";

    protected $fillable = [
        "id",
        "user_id",
        "chat_room_id",
        "role",
        "content",
        "audio",
        "translation",
        "created_at",
        "updated_at",
    ];
}
