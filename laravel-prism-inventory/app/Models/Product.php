<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // keep DB default behavior
    protected $fillable = ['name', 'category', 'quantity', 'price', 'is_archived'];
}