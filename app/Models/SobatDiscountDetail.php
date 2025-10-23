<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatDiscountDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'discount_detail';

    protected $fillable = [
        'discount_header_id',
        'discount_name',
        'product_uom_code',
        'min_qty',
        'max_qty',
        'discount_type',
        'discount_value',
        'charged_to_sdn'
    ];

    public $timestamps = true;

    public function discountHeader()
    {
        return $this->belongsTo(SobatDiscountHeader::class, 'discount_header_id');
    }
    public function productUom()
    {
        return $this->belongsTo(SobatProductUom::class, 'product_uom_code', 'product_uom_code');
    }
}
