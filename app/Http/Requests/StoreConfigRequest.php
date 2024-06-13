<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigRequest extends FormRequest
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
            'key' => 'required',
            'value'=> 'required',
            'label'=> 'required'
        ];
    }
    public function messages()
    {
        return [
            'key.required' => 'Please provide a Key',
            'value.required' => 'Please provide a Value',
            'label.required' => 'Please Provide a Label',

        ];
    }
}
