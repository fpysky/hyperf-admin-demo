<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

use Hyperf\Validation\Rule;

class PostValidate extends BaseValidate
{
    public function check($data, $func): string
    {
        $rule = $this->rule($data);
        if ($func == 'add') {
            unset($rule['id']);
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
            'name' => ['required', 'max:10', Rule::unique('post')->where(function ($query) {
                $query->where('delete_time', 0);
            })->ignore($data['id'])],
            'order' => 'required|integer|between:1,255',
            'status' => 'required|in:1,2',
            'mark' => 'max:255',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => '岗位ID不存在',
            'id.integer' => '岗位ID格式错误',
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
