<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatTax extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'tax';

    protected $fillable = [
        'tax_code',
        'tax_name',
        'tax_value',
        'status',
    ];

    public $timestamps = true;
}
