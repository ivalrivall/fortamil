<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PayOrderRequest extends FormRequest
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
            'payment_method_id' => 'required|numeric|min:2',
            'invoice_id' => 'required|numeric|min:1',
            'picture' => 'required|image',
            'amount' => 'required|numeric|min:1'
        ];
    }
}
