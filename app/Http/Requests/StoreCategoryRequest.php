<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'parent_id'=> 'required',
            'is_active'=> 'required',
            'photo_id'=>'mimes:jpeg,bmp,png,jpg'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'description.required' => 'Please provide a Description for Product',
            'parent_id.required' => 'Please Select a Parent Cateogory (Select None if there is no Parent Category)',
            'is_active.required' => 'Please select Category Status',
            'photo_id.mimes' => 'Invalid Image Format',
        ];
    }
}
