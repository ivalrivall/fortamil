<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseCreateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'picture' => 'required|file',
            'address' => 'required|string',
            'address_title' => 'required|string',
            'address_recipient' => 'required|string',
            'address_phone_recipient' => 'required|string',
            'city_id' => 'required|string',
            'district_id' => 'required|string',
            'province_id' => 'required|string',
            'village_id' => 'required|string',
            'postal_code' => 'required|string'
        ];
    }
}
