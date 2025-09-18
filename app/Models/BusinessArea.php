<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessArea extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'business_area';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'business_area_code',
        'business_area_name',
        'address',
        'npwp',
        'minimum_order_amount',
    ];
}
