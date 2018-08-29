<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FAQ;

class FAQController extends Controller
{
    protected $scope_keys = ['public','support','distributor','customer','partner'];

    public function create(Request $request){
        //return $request->all();
        $this->validate($request,['question'=>'required','answer'=>'required']);
        $faq = $this->create_faq($request->question,$request->answer);
        $prod_array = $this->get_prod_array($request);
        $this->set_faq_products($faq,$prod_array);
        $this->set_faq_scopes($faq,$request->only($this->scope_keys));
        $this->set_faq_categories($faq,$request->category);
        return redirect()->route('faq.index')->with(['info' => true, 'type' => 'success', 'text' => 'FAQ created successfully.']);
    }

    protected function create_faq($question,$answer){
        $created_by = request()->user()->partner;
        return FAQ::create(compact('question','answer', 'created_by'));
    }

    protected function get_prod_array($request){
        if(empty($request) || !$request->product) return null;
        $prod_array = [];
        foreach ($request->product as $product){
            //$editions = $request->edition[$product];
            if(!$request->edition || !array_key_exists($product,$request->edition) || !($editions = $request->edition[$product]) || in_array('All',$editions) || empty($editions)) $prod_array[] = ['product' => $product,'edition' => null];
            else foreach ($editions as $edition) $prod_array[] = compact('product','edition');
        }
        return $prod_array;
    }

    protected function set_faq_products($faq, $prod_array){
        $faq->Products()->delete();
        if($prod_array) $faq->Products()->createMany($prod_array);
    }

    protected function set_faq_scopes($faq, $array){
        $faq->Scope()->delete();
        $faq->Scope()->create($array);
    }

    protected function set_faq_categories($faq, $categories){
        $faq->Categories()->delete();
        if($categories) $faq->Categories()->create(compact('categories'));
    }

    public function ifv(Request $request){ FAQ::find(mb_substr($request->q,1))->increment('views'); }
    public function ifb(Request $request){ FAQ::find(mb_substr($request->q,1))->increment('benefits'); return $request->q; }

    public function update(FAQ $id, Request $request){
        $id->question = $request->question ?: $id->question;
        $id->answer = $request->answer ?: $id->answer;
        $id->save();
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Updated successfully.']);
    }

    public function scope(FAQ $id, Request $request){
        $this->set_faq_scopes($id,$request->only($this->scope_keys));
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Updated successfully.']);
    }

    public function product(FAQ $id, Request $request){
        $prod_array = $this->get_prod_array($request);
        $this->set_faq_products($id,$prod_array);
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Updated successfully.']);
    }

    public function category(FAQ $id, Request $request){
        $this->set_faq_categories($id,$request->category);
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Updated successfully.']);
    }

    public function delete(FAQ $id){
        $id->status = "INACTIVE"; $id->save();
        return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'Set to delete, Will get deleted in 48 hours.']);
    }

    public function undelete(FAQ $id){
        $id->status = "ACTIVE"; $id->save();
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Made Active']);
    }

    public function prt(){
        return \App\Models\Partner::select('code','name')->where('name','like','%'.request()->term.'%')->get();
    }

    public function fct(){
        $name = request()->c;
        return ($name) ? \App\Models\FAQAllCategory::create(compact('name')) : [];
    }

}
