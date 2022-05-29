<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Api\Wall;
use App\Models\Api\WallLike;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []];

        $user = auth()->user();

        $walls = Wall::all();

        foreach ($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['likes'] = 0;
            $walls[$wallKey]['liked'] = false;

            $likes = WallLike::where('wall_id', $wallValue['id'])->count();
            $walls[$wallKey]['likes'] = $likes;

            $meLikes = WallLike::where('wall_id', $wallValue['id'])
                ->where('user_id', $user['id'])
                ->count();

            if ($meLikes > 0) {
                $walls[$wallKey]['liked'] = true;
            }
        }
        $array['list'] = $walls;

        return $array;
    }

    public function like($id)
    {
        $array = ['error' => ''];

        $user = auth()->user();

        $meLikes = WallLike::where('wall_id', $id)
            ->where('user_id', $user['id'])
            ->count();

        if ($meLikes > 0) {
            WallLike::where('wall_id', $id)
                ->where('user_id', $user['id'])
                ->delete();

            $array['liked'] = false;
        } else {
            $newLike = new WallLike();
            $newLike->wall_id = $id;
            $newLike->user_id = $user['id'];
            $newLike->save();

            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('wall_id', $id)->count();

        return $array;
    }
}
