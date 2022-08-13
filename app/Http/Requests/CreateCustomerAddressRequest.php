<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerAddressRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'address_title' => 'present|nullable|string',
            'address_province_id' => 'required|numeric',
            'address_city_id' => 'required|numeric',
            'address_district_id' => 'required|numeric',
            'address_village_id' => 'required|numeric',
            'address_postal_code' => 'required|numeric',
            'address_recipient_name' => 'required|string',
            'address_recipient_phone' => 'required|numeric'
        ];
    }
}
