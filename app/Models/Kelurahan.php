<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'kelurahan';

    protected $fillable = [
        'kode_kecamatan',
        'kode_kelurahan',
        'nama_kelurahan'
    ];

    public $timestamps = true;
}
