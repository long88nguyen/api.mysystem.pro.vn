<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PronunciationDetail extends Model
{
    use HasFactory;

    protected $table = 'pronunciation_details';

    protected $fillable = [
        "pronunciation_id",
        "content",
        "audio",
        "ipa",
        "created_by",
    ];

    public function pronunciation_result()
    {
        return $this->hasOne(PronunciationResult::class, 'pronunciation_detail_id', 'id');
    }
}
