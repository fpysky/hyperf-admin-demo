<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class RuleStoreRequest extends FormRequest
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
            'name' => 'required|max:10',
            'type' => 'required|in:1,2,3,4',
            'icon' => 'required_unless:type,3,4',
            'path' => 'required_if:type,2|max:50',
            'route' => 'required_unless:type,1,2|max:100',
            'sort' => 'required|integer|between:1,255',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'parentId.required' => '选择所属权限',
            'parentId.integer' => '所属权限必须是整数',
            'name.required' => '填写权限名称',
            'name.max' => '权限名称长度不能超过10个字符',
            'type.required' => '选择类型',
            'type.in' => '选择类型错误',
            'icon.required_unless' => '权限类型为非按钮和接口时ICON不能为空',
            'path.required_if' => '权限类型为菜单时路由地址不能为空',
            'path.max' => '路由地址50个字符以内',
            'route.required_unless' => '权限类型为非目录和菜单时请求地址不能空',
            'route.max' => '请求地址100个字符以内',
            'sort.required' => '填写岗位顺序',
            'sort.integer' => '岗位顺序必须是1-255之间的整数',
            'sort.between' => '岗位顺序必须是1-255之间的整数',
            'status.required' => '选择状态',
            'status.in' => '选择状态错误',
        ];
    }
}