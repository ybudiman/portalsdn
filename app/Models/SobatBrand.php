<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatBrand extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'brands';

    protected $fillable = [
        'brand_name',
        'brand_description',
        'status',
        'brand_image',
    ];

    public $timestamps = true;
}
