<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:now'],
            'duration' => ['required', 'in:30,60,90'],
            'members' => ['required', 'integer', 'min:1'],
        ];
    }
}
