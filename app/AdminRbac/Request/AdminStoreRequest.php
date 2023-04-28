<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class AdminStoreRequest extends FormRequest
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
            'name' => 'required|max:10|unique:admin,name',
            'mobile' => 'required|unique:admin,mobile|regex:/^1[3456789]\d{9}$/',
            'email' => 'required|email',
            'password' => 'required_if:id,0|regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/',
            'rePassword' => 'required_if:id,0|same:password',
            'deptId' => 'required|integer',
            'postId' => 'required|integer',
            'status' => 'required|in:0,1',
            'roleIds' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '填写姓名',
            'name.max' => '姓名长度不能超过10个字符',
            'name.unique' => '姓名已存在',
            'mobile.required' => '填写手机号码',
            'mobile.regex' => '手机号码格式错误',
            'mobile.unique' => '手机号码已存在',
            'email.required' => '填写邮箱',
            'email.email' => '邮箱格式错误',
            'password.required_if' => '填写密码',
            'password.regex' => '登录密码必须同时含有数字和字母，且长度要在6-16位之间',
            'rePassword.required_if' => '填写确认密码',
            'rePassword.same' => '密码不一致',
            'deptId.required' => '选择部门',
            'deptId.integer' => '部门选中错误',
            'postId.required' => '选择岗位',
            'postId.integer' => '岗位选中错误',
            'status.required' => '选择启用状态',
            'status.in' => '启用状态错误',
            'roleIds.required' => '选择角色',
            'roleIds.in' => '选择角色错误',
        ];
    }
}
