<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class DeptStoreRequest extends FormRequest
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
            'parentId' => 'required|integer',
            'name' => 'required|max:10|unique:dept,name',
            'sort' => 'required|integer|between:1,255',
            'status' => 'required|in:0,1',
            'remark' => 'max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'parentId.required' => '选择上级部门',
            'parentId.integer' => '上级部门选择错误',
            'name.required' => '填写部门名称',
            'name.max' => '部门名称长度不能超过10个字符',
            'name.unique' => '部门已存在',
            'sort.required' => '填写排序',
            'sort.integer' => '填写的排序在1-255之间的整数',
            'sort.between' => '填写的排序在1-255之间的整数!',
            'status.required' => '选择状态',
            'status.in' => '启用状态错误',
            'remark.max' => '部门备注长度不能超过255个字符',
        ];
    }
}
