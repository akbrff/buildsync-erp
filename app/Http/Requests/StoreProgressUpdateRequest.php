<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_correction' => $this->boolean('is_correction'),
        ]);
    }

    public function rules(): array
    {
        return [
            'progress_percentage' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'recorded_at' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
            'is_correction' => ['boolean'],
        ];
    }
}