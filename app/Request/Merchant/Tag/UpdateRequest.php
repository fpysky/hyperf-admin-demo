<?php

declare(strict_types=1);

namespace App\Request\Merchant\Tag;

use Hyperf\Validation\Request\FormRequest;

class UpdateRequest extends FormRequest
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
            'status' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id必须是数值型',
            'status.required' => '状态不能为空',
            'status.numeric' => '状态必须是数值型',
        ];
    }
}
