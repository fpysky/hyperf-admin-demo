<?php

declare(strict_types=1);

namespace App\Request\Merchant\Tag;

use Hyperf\Validation\Request\FormRequest;

class CreateRequest extends FormRequest
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
            'name' => 'required|string|max:8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '地区名称必填',
            'name.string' => '地区名称必须是字符串类型',
            'name.max' => '最多8个字',
        ];
    }
}
