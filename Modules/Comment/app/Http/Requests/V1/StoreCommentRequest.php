<?php

namespace Modules\Comment\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'content' => ['required', 'string'],
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Merge route parameter into request data for validation if needed.
     */
    protected function prepareForValidation(): void
    {
        // Merge route parameter into request data so we can validate it
        $this->merge([
            'task_id' => $this->route('taskId'),
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_id.exists' => 'The selected task does not exist.',
        ];
    }
}
