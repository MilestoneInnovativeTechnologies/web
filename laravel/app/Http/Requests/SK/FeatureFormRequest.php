<?php

namespace App\Http\Requests\SK;

use App\Models\SK\Feature;
use Illuminate\Foundation\Http\FormRequest;

class FeatureFormRequest extends FormRequest
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
            'code' => 'sometimes|unique:sk_features,code',
            'type' => 'required',
            'name' => 'required',
        ];
    }

    public function store($data){ return (new Feature)->create($data); }

    public function prepareForValidation() {
        if(!$this->has('code') && $this->input('submit') === 'Add Feature') $this->merge(['code' => Feature::CODE()]);
        if(!$this->has('status')) $this->merge(['status' => 'Active']);
    }

//    public function messages()
//    {
//        return [
//            //
//        ];
//    }
}
