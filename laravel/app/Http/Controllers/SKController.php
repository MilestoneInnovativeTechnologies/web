<?php

namespace App\Http\Controllers;

use App\Http\Requests\SK\BranchFormRequest;
use App\Http\Requests\SK\ClientFormRequest;
use App\Models\SK\Branch;
use App\Models\SK\Client;
use App\Models\SK\EditionFeature;
use App\Models\SK\Feature;
use App\Models\SK\Subscription;
use Illuminate\Http\Request;

class SKController extends Controller
{
    public function add(ClientFormRequest $request){
        $request->store($request->only(['partner','name','code']));
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Client added successfully']);
    }

    public function addBranch($client, BranchFormRequest $request){
        $branch = $request->store($request->only(['client','name','code','edition','date','serial','ip_address','hostname','db_port','db_username','db_password','status']));
        return redirect()->route('sk.branch_features',['branch' => $branch->id]);
    }

    public function addBranchFeatures(Branch $branch, Request $request){
        if($request->getMethod() === 'POST'){
            $Features = Feature::pluck('default','id'); $features = [];
            foreach ($request->feature as $feature => $value){
                if($Features->has($feature) && $Features[$feature] !== $value)
                    $features[$feature] = compact('value');
            }
            $branch->Features()->sync($features);
            return redirect()->route('sk.branch_detail',['branch' => $branch->id]);
        } else {
            if($branch->edition !== '1') {
                $features = [];
                EditionFeature::where('edition',$branch->edition)->get()->each(function($ef) use(&$features){
                    $features[$ef->feature] = ['value' => $ef->value];
                });
                $branch->Features()->sync($features);
            }
            return redirect()->route('sk.branch_detail',['branch' => $branch->id]);
        }
    }

    public function update($id, Request $request){
        $Client = Client::withoutGlobalScopes()->find($id);
        $Client->update($request->only(['name','code','status']));
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Client details updated!!']);
    }

    public function updateBranch(Branch $branch, Request $request){
        $oldEdition = $branch->edition;
        $branch->update($request->only(['name','code','serial','edition','ip_address','status','hostname','db_port','db_username','db_password']));
        if($oldEdition !== $branch->edition || $branch->edition == 1) return redirect()->route('sk.branch_features',['branch' => $branch->id]);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Branch details updated!!']);
    }

    public function generate(Branch $branch){
        if($branch->serial && $branch->date) {
            $branch->key = Subscription::key($branch->serial, $branch->date); $branch->save();
            return back()->with(['info' => true, 'type' => 'success', 'text' => 'Key generated!!']);
        }
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Serial is required for generating key!!']);
    }

    public function subscription(Branch $branch, Request $request){
        if($request->expiry) {
            $branch->Subscriptions()->whereStatus('Active')->update(['status' => 'Expired']);
            $branch->Subscription()->save(new Subscription($request->only(['expiry','remarks'])));
            return back()->with(['info' => true, 'type' => 'success', 'text' => 'New subscription added!!']);
        }
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Expire date is mandatory for subscription code!!']);
    }

    public function cancel(Subscription $subscription){
        $subscription->status = 'Cancelled';
        $subscription->save();
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Subscription cancelled!!']);
    }
}
