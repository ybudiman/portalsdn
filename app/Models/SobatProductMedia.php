<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobatProductMedia extends Model
{
    use HasFactory;

    protected $connection = 'mysqlsobat';
    protected $table = 'product_media';

    protected $fillable = [
        'media_title',
        'media_filepath',
        'media_type',
        'isThumbnail'
    ];

    public $timestamps = true;

     // ---- RELATIONSHIPS ----
    public function category()
    {
        return $this->belongsTo(SobatProduct::class, 'product_id');
    }
}
