<?php

declare(strict_types=1);

namespace App\Request\Role;

use App\Request\FormRequest;

class UpStatusRequest extends FormRequest
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
            'ids' => 'required|array',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages():array
    {
        return [
            'ids.required' => 'ids不能为空',
            'ids.array' => 'ids必须是数组',
            'status.required' => 'status不能为空',
            'status.in' => 'status必须是0或1',
        ];
    }
}
