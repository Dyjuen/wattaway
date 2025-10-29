<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ControlDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('device'));
    }

    public function rules(): array
    {
        return [
            'channel' => 'required|integer|min:1|max:3',
            'action' => 'required|in:on,off,toggle',
            'duration' => 'nullable|integer|min:1|max:86400', // Max 24 hours
        ];
    }
}
