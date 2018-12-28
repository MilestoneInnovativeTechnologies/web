<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeyCodeEncDecController extends Controller
{
    public function encode(Request $request){
        $Array = $request->query(); if(empty($Array)) return '';
        $PArray = array_keys($Array);
        $VArray = array_values($Array);
        return implode("<br>",array_map(function($range) use($PArray,$VArray){ return KeyCodeController::Encode($PArray,$VArray); },range(1,10)));
    }

    public function decode($code = null){
        if(!$code) return '';
        $KeyValArray = KeyCodeController::Decode($code);
        $Array = array_combine($KeyValArray[0],$KeyValArray[1]);
        $Output = [];
        foreach($Array as $Key => $Val)
            $Output[] = implode(" ",[$Key,"=",$Val]);
        return implode("<br>",$Output);
    }
}
