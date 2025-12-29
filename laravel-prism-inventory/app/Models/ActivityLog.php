<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // uses DB default created_at
    protected $fillable = ['username', 'action', 'product_id', 'details', 'created_at'];
}