<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialStoreRequest extends FormRequest
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
            'price'=>array('required','regex:/^[0-9]/'),
            'is_active'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'sku.required' => 'Please provide a Sku',
            'price.required' => 'Please provide product price',
            'is_active.required' => 'Please select material Status',
        ];
    }

}
