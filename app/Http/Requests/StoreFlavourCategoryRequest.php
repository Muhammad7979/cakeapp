<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlavourCategoryRequest extends FormRequest
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
            'description'=>'required',
            'is_active'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Please provide a Name',
            'description.required' => 'Please provide a Description',
            'is_active.required' => 'Please select Flavour Category Active Status',

        ];
    }
}
