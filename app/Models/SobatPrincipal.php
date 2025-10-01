<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatPrincipal extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'principals';

    protected $fillable = [
        'principal_code',
        'principal_name',
        'principal_status',
        'status',
    ];

    public $timestamps = true;
}
