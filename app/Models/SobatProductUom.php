<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatProductUom extends Model
{
    protected $connection = 'mysqlsobat';
    protected $table = 'product_uom';

    protected $fillable = [
        'product_uom_code',
        'product_id',
        'uom_id',
        'level',
        'conversion_rate',
    ];

    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(SobatProduct::class, 'product_id', 'id');
    }
    public function uom()
    {
        return $this->belongsTo(SobatUom::class, 'uom_id', 'id');
    }
}
