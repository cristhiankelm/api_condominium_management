<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Api\Unit;
use App\Models\Api\UnitPeople;
use App\Models\Api\UnitPet;
use App\Models\Api\UnitVehicle;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function addPerson(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'birthdate' => 'required|date'
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $birthdate = $request->input('birthdate');

            $newPerson = new UnitPeople();
            $newPerson->unit_id = $id;
            $newPerson->name = $name;
            $newPerson->birthdate = $birthdate;
            $newPerson->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addVehicle(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'color' => 'required',
            'plate' => 'required',
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $color = $request->input('color');
            $plate = $request->input('plate');

            $newVehicle = new UnitVehicle();
            $newVehicle->unit_id = $id;
            $newVehicle->title = $title;
            $newVehicle->color = $color;
            $newVehicle->plate = $plate;
            $newVehicle->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addPet(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'race' => 'required',
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $race = $request->input('race');

            $newPet = new UnitPet();
            $newPet->unit_id = $id;
            $newPet->name = $name;
            $newPet->race = $race;
            $newPet->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function removePerson(Request $request, $id)
    {
        $array = ['error' => ''];

        $idItem = $request->input('id');
        if ($idItem) {
            UnitPeople::where('id', $idItem)
                ->where('unit_id', $id)
                ->delete();
        } else {
            $array['error'] = 'ID inexistente';
            return $array;
        }

        return $array;
    }
}
