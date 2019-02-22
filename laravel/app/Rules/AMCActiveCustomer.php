<?php

namespace App\Rules;

//use Illuminate\Contracts\Validation\Rule;
use App\Models\MaintenanceContract;
use Illuminate\Http\Request;

class AMCActiveCustomer// implements Rule
{

    private $InactiveStatuses = ['INACTIVE','JUST','EXPIRED'];
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
//    public function passes($attribute, $value)
//    {
//        return strtoupper($value) === $value;
//    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Maintenance Contract Expired, Please renew for further proceedings.';
    }

    public function test(Request $request){
        return $this->passes(null,$request->customer,null,null) ? 'ACTIVE' : 'INACTIVE';
    }

    public function passes($attribute, $customer, $parameters, $validator) {
        $contract = MaintenanceContract::withoutGlobalScopes(['own'])->where(compact('customer'))->first();
        if(is_null($contract)) return true;
        $status = $contract->status; $statusChunks = explode(" ",$status);
        if(in_array($statusChunks[0],$this->InactiveStatuses)) return false;
        return true;
    }
}