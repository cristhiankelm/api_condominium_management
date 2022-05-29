<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Api\Billet;
use App\Models\Api\Unit;

class BilletController extends Controller
{
    public function getAll(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');
        if ($property) {
            $user = auth()->user();

            $unit = Unit::where('id', $property)
                ->where('owner_id', $user['id'])
                ->count();

            if ($unit > 1) {
                $billets = Billet::where('unit_id', $property)->get();

                foreach ($billets as $billetKey => $billetValue) {
                    $billets[$billetKey]['file'] = asset('storage/' . $billetValue['file']);
                }

                $array['list'] = $billets;
            } else {
                $array['error'] = 'Esta unidade não é sua';
            }
        } else {
            $array['error'] = 'A propriedade é necessária';
        }

        return $array;
    }
}
