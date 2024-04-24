<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class PostUpdateRequest extends FormRequest
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
            'id' => 'required|integer',
            'name' => 'required|max:10',
            'order' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
            'mark' => 'max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id不能为空',
            'id.integer' => 'id必须为整型',
            'name.required' => '填写岗位名称',
            'name.max' => '岗位名称长度不能超过20个字符',
            'name.unique' => '岗位名称已经存在，换个岗位名称试试',
            'order.required' => '填写排序',
            'order.integer' => '填写的排序在1-255之间的整数',
            'order.between' => '填写的排序在1-255之间的整数!',
            'status.required' => '选择状态',
            'status.in' => '启用状态错误',
            'mark.max' => '部门备注长度不能超过255个字符',
        ];
    }
}
