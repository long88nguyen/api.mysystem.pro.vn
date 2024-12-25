<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PronunciationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pronunciation_detail_id',
        'content',
        'audio',
        'point',
        'rate',
    ];

    protected $table = 'pronunciation_results';

    

}
