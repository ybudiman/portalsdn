<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatUom extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'unit_of_measurement';

    protected $fillable = [
        'uom_name',
        'uom_description'
    ];
    
    public $timestamps = true;
}
