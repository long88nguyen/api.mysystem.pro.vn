<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'password'];

    protected $table = 'users';

    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public $timestamps = false;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return mixed|string[]
     */
    public function getFilterFields()
    {
        $staticObj = new static;
        if (empty($staticObj->filter_fields)) {
            return $staticObj->fillable;
        }
        return $staticObj->filter_fields;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return (new static)->getTable();
    }

    public function user()
    {
    }
}
