<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'sku' => 'required|string',
            'description' => 'required|string',
            'price_retail' => 'required|numeric',
            'price_grosir' => 'required|numeric',
            'price_modal' => 'required|numeric',
            'price_dropship' => 'required|numeric',
            'stock' => 'required|numeric',
            'weight' => 'required|numeric',
            'store_id' => 'required|numeric',
            'pictures' => 'required|array',
            'category_id' => 'required|numeric'
        ];
    }
}
