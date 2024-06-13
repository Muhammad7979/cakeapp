<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'salesman'=>'required',
            'customer_name'=>'required',
            'customer_phone'=>'required',
            'weight'=>array('required','regex:/^[1-9]/'),
            'quantity'=>'required',
            'total_price'=>'required',
            'advance_price'=>array('required','regex:/^[1-9][0-9]+|not_in:0/'),
            'payment_type'=>'required',
            'payment_status'=>'required',
            'order_type'=>'required',
            'order_status'=>'required',
            'delivery_date'=>'required',
            'delivery_time'=>'required',
            'remarks'=>'required',
            'branch_id'=>'required',
            'branch_code'=>'required',
            'assigned_to'=>'required',
            'user_id'=>'required',
            'priority'=>'required',
            'photo_id'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'salesman.required' => 'Salesman Name is required',
            'customer_name.required' => 'Custom Name is required',
            'customer_phone.required' => 'Customer Number is required',
            'quantity.required' => 'Quantity is required',
            'weight.required' => 'Product  Weight is required',
            'advance_price.required' => 'Advance Price is required',
            'payment_type.required' => 'Please Select Payment Type',
            'payment_status.required' => 'Payment Status is required',
            'order_type.required' => 'Please Select an Order Type',
            'order_status.required' => 'Order Status is required.',
            'delivery_date.required' => 'Please provide a DELIVERY DATE',
            'delivery_time.required' => 'Please provide a DELIVERY TIME',
            'remarks.required' => 'Please provide a Birthday Message',
            'branch_id.required' => 'Branch Id is Required',
            'branch_code.required' => 'Branch Code is Required',
            'assigned_to.required' => 'Please Select Assigned Branch From Assign To list',
            'user_id.required' => 'User Id is required',
            'priority.required' => 'Please Select Order Priority',
            'photo_id.required' => 'Image is required',
        ];
    }
}
