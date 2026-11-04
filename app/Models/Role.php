<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'slug'
    ];


    public static function getIdByRole(string $role)
    {
        return self::query()->where(
            'slug',
            '=',
            $role
        )->first();
    }
}
