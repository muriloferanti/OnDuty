<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    protected $fillable = ['external_id', 'type', 'status', 'payload'];

    protected $casts = [
        'payload' => 'array',
    ];
    
}
