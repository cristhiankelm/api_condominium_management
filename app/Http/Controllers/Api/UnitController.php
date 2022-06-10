<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Unit;
use App\Models\Api\UnitPeople;
use App\Models\Api\UnitPet;
use App\Models\Api\UnitVehicle;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function getInfo($id)
    {
        $array = ['error' => ''];

        $unit = Unit::find($id);
        if ($unit) {
            $peoples = UnitPeople::where('unit_id', $id)->get();
            $vehicles = UnitVehicle::where('unit_id', $id)->get();
            $pets = UnitPet::where('unit_id', $id)->get();

            foreach ($peoples as $pKey => $pValue) {
                $peoples[$pKey]['birthdate'] = date('d-m-Y', strtotime($pValue['birthdate']));
            }

            $array['peoples'] = $peoples;
            $array['vehicles'] = $vehicles;
            $array['pets'] = $pets;

        } else {
            $array['error'] = 'Propriedade inexistente';
            return $array;
        }

        return $array;
    }
}
