<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveReceptionPaymentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount_paid' => ['required','numeric','min:0',],
            'payment_method' => ['required','in:cash,transfer,multicaixa,tpa,reference,other',],
            'reference' => ['nullable','string','max:255',],
            'notes' => ['nullable','string',],
            'services' => ['required','array','min:1',],
            'services.*.product_id' => ['required','integer','exists:products,id',],
            'services.*.quantity' => ['required','numeric','min:0.01',],
            'services.*.discount' => ['nullable','numeric','min:0',],
            'services.*.notes' => ['nullable','string',],
        ];
    }
}
