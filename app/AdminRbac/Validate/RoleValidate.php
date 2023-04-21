<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

use Hyperf\Validation\Rule;

class RoleValidate extends BaseValidate
{
    public function check($data, $func): string
    {
        $rule = $this->rule($data);
        if ($func == 'add') {
            unset($rule['id']);
        }
        if (empty($data['desc'])) {
            unset($rule['desc']);
        }
        return $this->checkInfo($data, $rule, $this->message());
    }

    private function rule($data): array
    {
        return [
            'id' => 'required|integer',
            'name' => ['required', 'max:10', Rule::unique('role')->where(function ($query) {
                $query->where('delete_time', 0);
            })->ignore($data['id'])],
            'desc' => 'max:20',
            'order' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID格式不对',
            'name.required' => '填写角色名称',
            'name.max' => '角色名称长度不能超过10个字符',
            'name.unique' => '角色名称已经存在，换个名称试试',
            'desc.max' => '描述最多20个字符',
            'order.required' => '填写排序',
            'order.integer' => '填写的排序在1-255之间的整数',
            'order.between' => '填写的排序在1-255之间的整数!',
            'status.in' => '启用状态错误',
        ];
    }
}
