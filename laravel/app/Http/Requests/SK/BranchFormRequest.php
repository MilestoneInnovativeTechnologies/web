<?php

namespace App\Http\Requests\SK;

use App\Models\SK\Branch;
use Illuminate\Foundation\Http\FormRequest;

class BranchFormRequest extends FormRequest
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
            'client' => 'required',
            'name' => 'required',
            'code' => 'required',
            'edition' => 'required',
        ];
    }

    public function prepareForValidation() {
        $this->merge([
            'status' => $this->get('status','Active'),
            'date' => $this->get('date',date('Y-m-d')),
            'port' => $this->get('port','3306'),
        ]);
    }

    public function store($data){ return (new Branch)->create($data); }

//    public function messages()
//    {
//        return [
//            //
//        ];
//    }
}
