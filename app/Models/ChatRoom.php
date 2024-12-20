<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = "chat_rooms";

    protected $fillable = [
        'bot_name', 
        'bot_avatar', 
        'bot_description', 
        'user_id', 
        'name', 
        'text_to_speech_model', 
        'voice_model', 
        'speech_to_text_model', 
        'chat_gpt_model', 
        'language'
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id', 'id');
    } 
}
