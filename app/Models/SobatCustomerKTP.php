<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatCustomerKTP extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'user_ktp';

    protected $fillable = [
        'kode_provinsi_ktp',
        'kode_kota_ktp',
        'nama',
        'NIK',
        'TTL',
        'alamat',
        'jenis_kelamin',
        'rt_rw',
        'kode_kelurahan',
        'ktp_image',
        'kode_kecamatan',
        'status',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = true;
}
