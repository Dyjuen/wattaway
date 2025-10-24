<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by auth.device middleware
    }

    public function rules(): array
    {
        return [
            'voltage' => 'required|numeric|between:0,300',
            'current' => 'required|numeric|between:0,100',
            'power' => 'required|numeric|between:0,30000',
            'energy' => 'required|numeric|min:0',
            'frequency' => 'sometimes|numeric|between:45,65',
            'power_factor' => 'sometimes|numeric|between:0,1',
            'timestamp' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'voltage.between' => 'Voltage must be between 0 and 300V',
            'frequency.between' => 'Frequency must be between 45 and 65Hz',
        ];
    }
}
