<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'name'=>'required',
            'code'=>'required',
            'address'=>'required',
            'phone'=>'required',
            'is_active'=>'required',

        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'code.required' => 'Please provide a Code for Branch',
            'address.required' => 'Please provide Branch Address',
            'phone.required' => 'Please Provide Branch Phone Number',
            'is_active.required' => 'Please Provide Branch Status',
        ];
    }

}
