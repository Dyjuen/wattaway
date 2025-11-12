<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('device'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'channel' => 'required|integer|min:1|max:3',
            'action' => 'required|in:on,off',
            'schedule_type' => 'required|in:once,daily,weekly,custom',
            'scheduled_time' => 'required|date_format:H:i',
            'days_of_week' => 'required_if:schedule_type,weekly|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strip_tags($this->name),
        ]);
    }
}
