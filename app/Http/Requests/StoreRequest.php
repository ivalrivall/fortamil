<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'marketplace_id' => 'required',
            'picture' => 'required|file',
            'address' => 'required|string',
            'address_title' => 'required|string',
            'address_recipient' => 'required|string',
            'address_phone_recipient' => 'required|string',
        ];
    }
}
