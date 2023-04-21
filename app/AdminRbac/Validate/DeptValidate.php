<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

use Hyperf\Validation\Rule;

class DeptValidate extends BaseValidate
{
    public function check($data, $func): string
    {
        $rule = $this->rule($data);
        if ($func == 'add') {
            unset($rule['id']);
        }
        if (empty($data['username'])) {
            unset($rule['username']);
        }
        if (empty($data['mobile'])) {
            unset($rule['mobile']);
        }
        if (empty($data['email'])) {
            unset($rule['email']);
        }
        if (empty($data['mark'])) {
            unset($rule['mark']);
        }

        return $this->checkInfo($data, $rule, $this->message());
    }

    private function rule($data): array
    {
        return [
            'id' => 'required|integer',
            'parent_id' => 'required|integer',
            'name' => ['required', 'max:10', Rule::unique('dept')->where(function ($query) {
                $query->where('delete_time', 0);
            })->ignore($data['id'])],
            'username' => 'max:10',
            'mobile' => 'regex:' . $this->help->mobilePreg(),
            'email' => 'email',
            'order' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
            'mark' => 'max:255',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => '部门ID不存在',
            'id.integer' => '部门ID格式错误',
            'parent_id.required' => '选择上级部门',
            'parent_id.integer' => '上级部门选择错误',
            'name.required' => '填写部门名称',
            'name.unique' => '部门名称已经存在，换个名称试试',
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
