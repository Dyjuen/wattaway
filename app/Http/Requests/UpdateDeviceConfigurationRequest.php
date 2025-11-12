<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('device'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->route('type');

        $rules = [
            'configuration' => 'required|array',
        ];

        switch ($type) {
            case 'scheduler':
                $rules['configuration.start_time'] = 'required|date_format:H:i';
                $rules['configuration.end_time'] = 'required|date_format:H:i';
                break;
            case 'timer':
                $rules['configuration.duration'] = 'required|integer|min:1|max:120';
                break;
            case 'watt_limit':
                $rules['configuration.limit'] = 'required|integer|min:1|max:10000';
                break;
        }

        return $rules;
    }
}
