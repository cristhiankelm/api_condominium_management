<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\FoundAndLost;
use Illuminate\Http\Request;

class FoundAndLostController extends Controller
{
    public function getAll(Request $request)
    {
        $array = ['error' => ''];

        $lost = FoundAndLost::where('status', 'LOST')
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $recovered = FoundAndLost::where('status', 'RECOVERED')
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($lost as $lostKey => $lostValue) {
            $lost[$lostKey]['datecreated'] = date('d/m/Y', strtotime($lostValue['datecreated']));
            $lost[$lostKey]['photo'] = asset('storage/' . $lostValue['photo']);
        }

        foreach ($recovered as $recKey => $recValue) {
            $recovered[$recKey]['datecreated'] = date('d/m/Y', strtotime($recValue['datecreated']));
            $recovered[$recKey]['photo'] = asset('storage/' . $recValue['photo']);
        }

        $array['lost'] = $lost;
        $array['recovered'] = $recovered;

        return $array;
    }
}
