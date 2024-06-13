<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id'=>'required',
            'weight'=>array('required','regex:/^[1-9]/'),
            'is_active'=>'required',
            'price'=>array('required','regex:/^[0-9]/'),
            'photo_id'=>'mimes:jpeg,bmp,png,jpg'

        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'sku.required' => 'Please provide a SKU',
            'category_id.required' => 'Please Select Product Category',
            'is_active.required' => 'Please select Product Active Status',
            'weight.required' => 'Please provide product weight',
            'price.required' => 'Please provide a price for product',
            'photo_id.mimes' => 'Invalid Image Format',
        ];
    }
}
