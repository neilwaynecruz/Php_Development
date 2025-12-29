<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'uid';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // users table has no created_at/updated_at
    protected $fillable = ['username', 'password', 'role'];
}