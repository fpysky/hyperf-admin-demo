<?php

declare(strict_types=1);

namespace App\Request\Scenario;

use Hyperf\Validation\Request\FormRequest;

class StatusRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'id' => 'required|numeric',
            'status' => 'required|numeric|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id必须是数值型',
            'status.required' => 'status不能为空',
            'status.numeric' => 'status必须是数值型',
            'status.in' => 'status必须在0和1之间',
        ];
    }
}
