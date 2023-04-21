<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class DeptUpdateRequest extends FormRequest
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
            'parentId' => 'required|integer',
            'name' => 'required|max:10',
            'username' => 'max:10',
            'mobile' => 'regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/',
            'email' => 'email',
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
            'parentId.required' => '选择上级部门',
            'parentId.integer' => '上级部门选择错误',
            'name.required' => '填写部门名称',
            'name.max' => '部门名称长度不能超过10个字符',
            'username.max' => '负责人姓名长度不能超过10个字符',
            'mobile.regex' => '手机号码格式不对',
            'email.email' => '邮箱号码格式不对',
            'order.required' => '填写排序',
            'order.integer' => '填写的排序在1-255之间的整数',
            'order.between' => '填写的排序在1-255之间的整数!',
            'status.required' => '选择状态',
            'status.in' => '启用状态错误',
            'mark.max' => '部门备注长度不能超过255个字符',
        ];
    }
}
