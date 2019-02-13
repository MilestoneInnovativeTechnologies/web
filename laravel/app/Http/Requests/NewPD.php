<?php

namespace App\Http\Requests;

use App\Models\PD;
use Illuminate\Foundation\Http\FormRequest;

class NewPD extends FormRequest
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
        ];
    }

    public function messages(){
        return [
            "customer.required"			=>	"The Customer cannot be empty.",
            "seq.required"				=>	"Please select a product",
        ];
    }

    public function store(){
        return PD::create($this->only(['customer','seq','url_web','url_interact','url_api','date_start','date_end','code']));
    }

    public function update($id){
        $pd = PD::find($id);
        return $pd->update($this->only(['customer','seq','url_web','url_interact','url_api','date_start','date_end'])) ? $pd : false;
    }
}
