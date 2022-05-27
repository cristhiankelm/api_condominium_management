<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundAndLost extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'found_and_lost';
}
