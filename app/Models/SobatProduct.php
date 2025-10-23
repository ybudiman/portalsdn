<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatProduct extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'products';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_description',
        'product_image',
        'isNew',
        'isFeatured',
        'external_product_code',
        'category_id',
        'taxable',
        'tax_id',
        'principal_id',
        'brand_id',
        'min_order_uom_id',
        'status',
    ];

    public $timestamps = true;

     // ---- RELATIONSHIPS ----
    public function category()
    {
        return $this->belongsTo(SobatCategory::class, 'category_id');
    }

    public function principal()
    {
        return $this->belongsTo(SobatPrincipal::class, 'principal_id');
    }

    public function brand()
    {
        return $this->belongsTo(SobatBrand::class, 'brand_id');
    }

    public function tax()
    {
        return $this->belongsTo(SobatTax::class, 'tax_id');
    }

    public function minOrderUom()
    {
        return $this->belongsTo(SobatUom::class, 'min_order_uom_id');
    }
}
