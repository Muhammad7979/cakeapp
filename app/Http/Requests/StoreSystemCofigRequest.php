<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSystemCofigRequest extends FormRequest
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
            //
            'branch_Code' => 'required',
            'branch_address'=> 'required',
            'branch_name'=> 'required',
            'system_id'=> 'required',
            'branch_number'=> 'required',
            'return_policy'=> 'required',

        ];
    }
    public function messages()
    {
        return [
            'branch_Code.required' => 'Branch Code is required.',
            'branch_address.required' => 'Branch Address is required',
            'branch_name.required' => 'Branch Name is required',
            'system_id.required' => 'Please enter a SYSTEM ID',
            'branch_number.required' => 'Please enter a valid Branch Number',
            'return_policy.required' => 'Return Policy is required'
        ];
    }
}
