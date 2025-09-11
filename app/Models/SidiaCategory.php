<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'sidia_categories';

    protected $fillable = [
        'category_code',
        'category_name',
    ];

    public $timestamps = true;
}