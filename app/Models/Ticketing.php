<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticketing extends Model
{
    
    use HasFactory;
    protected $connection = 'sqlsrv';

    protected $table = "tickets";

    protected $fillable = [
        'project',
        'epic',
        'code',
        'ref_code',
        'name',
        'content',
        'product',
        'environment',
        'owner',
        'pic_dev',
        'pic_QA',
        'pic_SIT',
        'pic_UAT',
        'status',
        'type',
        'priority',
        'created_at',
        'related_tickets',
        'inprogress_at',
        'resolved_at',
        'closed_at',
    ];

    public $timestamps = false;
}
