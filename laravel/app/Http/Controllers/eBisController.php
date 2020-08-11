<?php

namespace App\Http\Controllers;

use App\Http\Requests\eBisFormRequest;
use App\Models\eBis;
use App\Models\eBisSubscription;
use Illuminate\Http\Request;

class eBisController extends Controller
{
    public function store(eBisFormRequest $request){
        $request->merge(['code' => null]);
        $request->store($request->all());
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Client added successfully!']);
    }

    public function subscription(eBis $eBis, Request $request){
        $subscription = new eBisSubscription($request->only(['domain','package','start','end']));
        $eBis->Subscriptions()->save($subscription);
        eBisSubscription::rearrange();
        return redirect()->route('ebis.view',$eBis->id)->with(['info' => true, 'text' => 'New subscription added successfully!!', 'type' => 'success']);
    }
}
