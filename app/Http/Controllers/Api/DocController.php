<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Api\Doc;

class DocController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Doc::all();

        foreach ($docs as $docKey => $docValue){
            $docs[$docKey]['file'] = asset('storage/' . $docValue['file']);
        }

        $array['list'] = $docs;

        return $array;
    }
}
