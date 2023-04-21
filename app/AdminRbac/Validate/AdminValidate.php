<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

use Hyperf\Validation\Rule;

class AdminValidate extends BaseValidate
{
    public function check($data, $func): string
    {
        $rule = $this->rule($data);
        if ($func == 'add') {
            unset($rule['id']);
        }
        return $this->checkInfo($data, $rule, $this->message());
    }

    private function rule($data): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|max:10',
            'mobile' => ['required', 'regex:' . $this->help->mobilePreg(), Rule::unique('admin')->where(function ($query) {
                $query->where(['delete_time' => 0]);
            })->ignore($data['id'])],
            'email' => 'required|email',
            'password' => 'required_if:id,0|regex:' . $this->help->passwordPreg(),
            'retpassword' => 'required_if:id,0|same:password',
            'dept_id' => 'required|integer',
            'post_id' => 'required|integer',
            'status' => 'required|in:1,2',
            'roleids' => 'required|array',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID格式错误',
            'name.required' => '填写姓名',
            'name.max' => '姓名长度不能超过10个字符',
            'mobile.required' => '填写手机号码',
            'mobile.regex' => '手机号码格式错误',
            'mobile.unique' => '手机号码已存在，换个手机试试',
            'email.required' => '填写邮箱',
            'email.email' => '邮箱格式错误',
            'password.required_if' => '填写密码',
            'password.regex' => '登录密码必须同时含有数字和字母，且长度要在6-16位之间',
            'retpassword.required_if' => '填写确认密码',
            'retpassword.same' => '密码不一致',
            'dept_id.required' => '选择部门',
            'dept_id.integer' => '部门选中错误',
            'post_id.required' => '选择岗位',
            'post_id.integer' => '岗位选中错误',
            'status.required' => '选择启用状态',
            'status.in' => '启用状态错误',
            'roleids.required' => '选择角色',
            'roleids.in' => '选择角色错误',
        ];
    }
}
