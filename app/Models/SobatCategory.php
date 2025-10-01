<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatCategory extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'category_description',
        'category_image',
        'status',
    ];

    public $timestamps = true;
}
