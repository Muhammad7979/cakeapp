<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'=>'required',
            'email'=>'required',
            'role_id'=>'required',
            'is_active'=>'required',
            'password'=>'required|confirmed',
            'is_admin'=>'required',
            'photo_id'=>'mimes:jpeg,bmp,png,jpg'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'User NAME is required',
            'email.required' => 'User EMAIL is required',
            'role_id.required' => 'Please assign user a role',
            'is_active.required' => 'Please select ACTIVE STATUS for user',
            'is_admin.required' => 'Please Select USER ADMIN STATUS',
            'photo_id.mimes' => 'Invalid Image Format',
        ];
    }
}
