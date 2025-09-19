<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatCustomerDomicile extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'domisili';

    protected $fillable = [
        'alamat',
        'kode_provinsi',
        'kode_kota',
        'kode_kelurahan',
        'kode_pos',
        'longitude',
        'langitude',
        'status',
        'image_rumah',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = true;
}
