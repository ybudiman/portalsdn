<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;
    protected $table = "cabang";
    protected $primaryKey = "kode_cabang";
    public $incrementing = false;
    protected $guarded = [];
}
