<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPeople extends Model
{
    use HasFactory;

    protected $hidden = [
        'unit_id'
    ];

    public $timestamps = false;
    public $table = 'unit_peoples';
}
