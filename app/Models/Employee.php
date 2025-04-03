<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'description',
        'age',
    ];

    protected $appends = [
        'level',
    ];

    protected $casts = [
        'description' => 'array',
    ];

    // protected function description(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn($value) => json_decode($value ?? '', true),
    //         set: fn($value) => json_encode($value ?? '', true)
    //     );
    // }

    public function getLevelAttribute() :String
    { 
        if($this->age < 20){
            return 'junior';
        }elseif($this->age >= 20 && $this->age < 30){
            return 'middle';
        }else{
            return 'senior';
        }
    }

    // Định dạng ngày tạo
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }
    
    // Định dạng ngày cập nhật
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }
}
