<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WallLike extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'wall_likes';
}
