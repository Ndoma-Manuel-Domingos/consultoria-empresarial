<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveReceptionTriageRequest extends FormRequest
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
            'business_area' => ['nullable','string','max:255',],
            'company_size' => ['nullable','string','max:100',],
            'need_type' => ['nullable','string','max:255',],
            'urgency' => ['nullable','string','max:100',],
            'business_situation' => ['nullable','string',],
            'presented_problem' => ['required','string',],
            'requested_solution' => ['nullable','string',],
            'documents_presented' => ['nullable','string',],
            'documents_pending' => ['nullable','string',],
            'notes' => ['nullable','string',],
        ];
    }
}
