<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatDiscountHeader extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'discount_header';

    protected $fillable = [
        'discount_name',
        'level',
        'business_area_code',
        'start_date',
        'finish_date'
    ];

    public $timestamps = true;

    public function details()
    {
        return $this->hasMany(SobatDiscountDetail::class, 'discount_header_id', 'id');
    }

}
