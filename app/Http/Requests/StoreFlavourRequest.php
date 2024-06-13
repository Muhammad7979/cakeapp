<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlavourRequest extends FormRequest
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
            'sku'=>'required',
            'price'=>array('required','regex:/^[1-9][0-9]+|not_in:0/'),
            'flavourCategory_id'=>'required',
            'is_active'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'sku.required' => 'Please provide a SKU',
            'flavourCategory_id.required' => 'Please Select Flavour Category',
            'is_active.required' => 'Please select Flavour Active Status',
            'price.required' => 'Please provide a price for product',

        ];
    }
}
