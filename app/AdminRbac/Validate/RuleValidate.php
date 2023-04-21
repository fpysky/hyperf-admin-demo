<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

class RuleValidate extends BaseValidate
{
    public function check($data, $func): string
    {
        $rule = $this->rule();
        if ($func == 'add') {
            unset($rule['id']);
        }
        return $this->checkInfo($data, $rule, $this->message());
    }

    private function rule(): array
    {
        return [
            'id' => 'required|integer',
            'parent_id' => 'required|integer',
            'name' => 'required|max:10',
            'type' => 'required|in:1,2,3,4',
            'icon' => 'required_unless:type,3,4',
            'path' => 'required_if:type,2|max:50',
            'route' => 'required_unless:type,1,2|max:100',
            'order' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => 'id不存在',
            'id.integer' => 'id格式不对',
            'parent_id.required' => '选择所属权限',
            'parent_id.integer' => '所属权限必须是整数',
            'name.required' => '填写权限名称',
            'name.max' => '权限名称长度不能超过10个字符',
            'type.required' => '选择类型',
            'type.in' => '选择类型错误',
            'icon.required_unless' => '权限类型为非按钮和接口时ICON不能为空',
            'path.required_if' => '权限类型为菜单时路由地址不能为空',
            'path.max' => '路由地址50个字符以内',
            'route.required_unless' => '权限类型为非目录和菜单时请求地址不能空',
            'route.max' => '请求地址100个字符以内',
            'order.required' => '填写岗位顺序',
            'order.integer' => '岗位顺序必须是1-255之间的整数',
            'order.between' => '岗位顺序必须是1-255之间的整数',
            'status.required' => '选择状态',
            'status.in' => '选择状态错误',
        ];
    }
}
