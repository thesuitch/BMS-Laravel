<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'order_details.order_id' => 'required',
            'order_details.customer_id' => 'required',
            'order_details.est_delivery_date' => 'required',
            'order_details.order_date' => 'required',
            'order_details.side_mark' => 'required',
            'order_details.subtotal' => 'required',
            'order_details.misc_total' => 'required',
            'order_details.tax_percentage' => 'required',
            'order_details.invoice_discount' => 'required',
            'order_details.grand_total' => 'required',
        ];

        if (request('order_details.shipping_address.different_address') == 1) {

            $rules['order_details.shipping_address.different_address_type'] = 'required';

            if (request('order_details.shipping_address.different_address_type') == 1) {
            $rules['order_details.shipping_address.receiver_name'] = 'required';
            $rules['order_details.shipping_address.receiver_phone_no'] = 'required';
            $rules['order_details.shipping_address.receiver_email'] = 'required';
            $rules['order_details.shipping_address.receiver_address'] = 'required';
            }
            elseif(request('order_details.shipping_address.different_address_type') == 2){
                $rules['order_details.shipping_address.address_type'] = 'required';

            }
        }
      
        return $rules;
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'   => 'error',
            'code'   => 422,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }
}
