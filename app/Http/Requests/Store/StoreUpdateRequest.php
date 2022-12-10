<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            'id' => 'bail|required|numeric|min:1',
            'name' => 'bail|required|string',
            'marketplace_id' => 'bail|required',
            'picture' => 'bail|nullable|file',
            'address' => 'bail|required|string',
            'latest_address_id' => 'bail|required|numeric',
            'address_title' => 'bail|required|string',
            'address_recipient' => 'bail|required|string',
            'address_phone_recipient' => 'bail|required|string',
            'city_id' => 'bail|required|string',
            'district_id' => 'bail|required|string',
            'province_id' => 'bail|required|string',
            'village_id' => 'bail|required|string',
            'postal_code' => 'bail|required|string'
        ];
    }
}
