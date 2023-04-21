<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class RoleStoreRequest extends FormRequest
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
            'name' => 'required|max:10',
            'desc' => 'max:20',
            'sort' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '填写角色名称',
            'name.max' => '角色名称长度不能超过10个字符',
            'desc.max' => '描述最多20个字符',
            'sort.required' => '填写排序',
            'sort.integer' => '填写的排序在1-255之间的整数',
            'sort.between' => '填写的排序在1-255之间的整数!',
            'status.in' => '启用状态错误',
        ];
    }
}
