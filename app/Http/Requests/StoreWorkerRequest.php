<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'worker_code' => ['required', 'string', 'max:255', 'unique:workers,worker_code'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ];
    }
}