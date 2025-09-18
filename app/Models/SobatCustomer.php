<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatCustomer extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'users';

    protected $fillable = [
        'fullname',
        'profile_picutre',
        'verified',
        'employee',
        'verified',
        'employee_id',
        'external_customer_id',
        'default_delivery_type',
        'business_area_code'
    ];

    public $timestamps = true;
}
