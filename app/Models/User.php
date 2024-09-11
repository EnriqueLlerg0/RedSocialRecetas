<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'users';

    protected $fillable=
        [
            'id',
            'user',
            'email',
            'password',
            'img',
            'roll',
            'updated_at',
            'created_at',
        ];
}
