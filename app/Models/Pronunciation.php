<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pronunciation extends Model
{
    use HasFactory;

    protected $table = 'pronunciations';

    protected $fillable = [
        'topic_name',
        'user_id'
    ];

    public function pronunciation_details()
    {
        return $this->hasMany(PronunciationDetail::class, 'pronunciation_id', 'id');
    }
}
