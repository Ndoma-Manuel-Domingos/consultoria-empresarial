<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReceptionAppointmentRequest extends FormRequest
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
            'client_id' => ['required','integer','exists:clients,id',],
            'appointment_date' => ['required','date',],
            'appointment_time' => ['nullable','date_format:H:i',],
            'priority' => ['required','in:low,normal,high,urgent',],
            'reason' => ['nullable','string','max:5000',],
            'service_type' => ['nullable','string','max:255',],
            'reception_notes' => ['nullable','string','max:5000',],
        ];
    }
}
