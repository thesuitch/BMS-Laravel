<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderUpdateItem extends FormRequest
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
        return  [
            'row_id' => 'required',
            'product_id' => 'required',
            'category_id' => 'required',
            'list_price' => 'required',
            'qty' => 'required',
            'upcharge_price' => 'required',
            // 'upcharge_label' => 'required',
            // 'upcharge_details' => 'required',
            'pattern_id' => 'required',
            'width' => 'required',
            'height' => 'required',
            // 'room_index' => 'required',
            'room' => 'required',
            'discount' => 'required',
            'attributes' => 'required',
        ];
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
}
