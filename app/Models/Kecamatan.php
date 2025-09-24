<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'kecamatan';

    protected $fillable = [
        'kode_kota',
        'kode_kecamatan',
        'nama_kecamatan'
    ];

    public $timestamps = true;
}
