<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'store_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            'marketplace_picture_label' => 'required|image',
            'marketplace_number_invoice' => 'required',
            'number_resi' => 'required|string',
            'customer_plain_shipment_address' => 'required|string',
            'notes' => 'present|string|nullable',
            'cart_id' => 'present|array|min:1',
            'cart_id.*' => 'required|numeric|distinct',
            'warehouse_id' => 'required|numeric',
            'payment_method_id' => 'required|numeric'
        ];
    }
}
