<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'kota';

    protected $fillable = [
        'kode_provinsi',
        'kode_kota',
        'nama_kota'
    ];

    public $timestamps = true;
}
