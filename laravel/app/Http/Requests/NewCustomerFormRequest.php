<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewCustomerFormRequest extends FormRequest
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
          "code"				=>	"required|unique:partners,code",
          //"name"				=>	"required|unique:partners,name",
					"country"			=>	"required|exists:partner_countries,country,partner," . ($this->user()->partner),
					"email"				=>	"required|unique:partner_logins,email",
					"product"			=>	"required|exists:partner_products,product,partner," . ($this->user()->partner),
					"edition"			=>	"required|exists:partner_products,edition,partner," . ($this->user()->partner) . ",product," . ($this->product),
					"presaleend"	=>	"required|after:yesterday",
					"dealer"			=>	"nullable|exists:partner_relations,partner,parent," . ($this->user()->partner)
        ];
    }
	
		public function messages(){
			
			return [
				"code.required"			=>	"The Customer code field cannot be empty.",
				"code.unique"				=>	"The Customer code is already taken, Try a new code.",
				"name.required"			=>	"The Name field cannot be empty.",
				//"name.unique"				=>	"The Name is already taken, Try a new name.",
				"country.required"	=>	"The Country cannot be empty.",
				"country.exists"		=>	"You are not authorized to add a customer for this country.",
				"email.required"		=>	"Email is Mandatory, Please fill.",
				"email.unique"			=>	"Email is already in use.",
				"product.required"	=>	"Please mention the product.",
				"product.exists"		=>	"You are not assined to sell this product.",
				"edition.required"	=>	"Please mention the Edition.",
				"edition.exists"		=>	"You are not assined to sell this edition.",
				"presaleend.required"	=>	"Presale end date is a required field.",
				"presaleend.after"	=>	"Presale end date should be somewhat future date.",
				"dealer.exists"			=>	"Selected Dealer is not valid."
			];
			
		}
}
