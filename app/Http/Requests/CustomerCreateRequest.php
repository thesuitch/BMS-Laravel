<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class CustomerCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'billing_address_label' => 'required',
            'username' => 'required',
            'password' => 'required',
        ];

        // Check if enable_customer_account_type is checked, then add required
        if (request('enable_customer_account_type') == 1) {
            $rules['customer_type'] = 'required';
        }
        // Check if customer_type is 'business', then add required
        if (request('customer_type') == 'business') {
            $rules['company'] = 'required';
            $rules['order_prefix'] = 'required';
        }

        if (!empty(request('zone'))) {
            $rules['zone'] = 'integer';
        }

        

        // Check if different_shipping_address is checked, then add required
        if (request('different_shipping_address') == 1) {
            $rules['shipping_first_name'] = 'required';
            $rules['shipping_last_name'] = 'required';
            $rules['shipping_email'] = 'required';
            $rules['shipping_phone'] = 'required';
            $rules['shipping_address'] = 'required';
            $rules['shipping_address_label'] = 'required';
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
        ],422));
    }
    public function messages() //OPTIONAL
    {
        return [
            'customer_type.required' => "The customer_type field is required. Please select either 'business' or 'personal' ",
        ];
    }
}
