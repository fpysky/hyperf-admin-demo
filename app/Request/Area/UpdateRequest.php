<?php

declare(strict_types=1);

namespace App\Request\Area;

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
            'name' => 'required|string|max:8',
            'sortOrder' => 'required|integer',
            'pid' => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id必须是数值型',
            'name.required' => '地区名称必填',
            'name.string' => '地区名称必须是字符串类型',
            'name.max' => '最多8个字',
            'sortOrder.required' => '排序必填',
            'sortOrder.integer' => '排序必须是数值类型',
            'pid.integer' => '父id必须是数值类型',
        ];
    }
}
