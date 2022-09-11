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
            'marketplace_picture_label' => 'required|file',
            'marketplace_number_invoice' => 'required',
            'number_resi' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|numeric',
            'customer_province_id' => 'required|numeric',
            'customer_city_id' => 'required|numeric',
            'customer_district_id' => 'required|numeric',
            'customer_village_id' => 'required|numeric',
            'customer_postal_code' => 'required|numeric',
            'customer_recipient_name' => 'required|string',
            'customer_recipient_phone' => 'required|numeric',
            'notes' => 'present|string|nullable',
            'cart_id' => 'present|array|min:1'
        ];
    }
}
