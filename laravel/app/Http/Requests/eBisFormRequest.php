<?php

namespace App\Http\Requests;

use App\Models\eBis;
use App\Models\eBisSubscription;
use Illuminate\Foundation\Http\FormRequest;

class eBisFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer' => 'required',
            'seq' => 'required',
            'product' => 'required',
            'package' => 'required',
            'start' => 'required',
            'end' => 'required',
            'host' => 'required'
        ];
    }

    public function store($data){
        $eBis = (new eBis)->create(array_only($data,['code','customer','seq','product']));
        $subscriptions = new eBisSubscription(array_only($data,['package','start','end','host','database','username','password']));
        $eBis->Subscriptions()->save($subscriptions);
        eBisSubscription::rearrange();
        return $eBis;
    }

    public function messages()
    {
        return [
            'product.required' => 'Error in choosing product. Please info the authorities!',
            'seq.required' => 'Error in choosing registration. Please info the authorities!',
        ];
    }
}
