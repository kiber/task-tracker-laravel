<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecurringTaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable'],
            'frequency' => ['required', Rule::enum(TaskFrequency::class)],
            'weekly_days' => [
                'nullable',
                'array',
                Rule::requiredIf(fn (): bool => $this->input('frequency') === TaskFrequency::Weekly->value),
            ],
            'weekly_days.*' => ['string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'monthly_day' => [
                'nullable',
                'integer',
                'between:1,31',
                Rule::requiredIf(fn (): bool => $this->input('frequency') === TaskFrequency::Monthly->value),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
