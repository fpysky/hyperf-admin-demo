<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class DeptCode extends CommonCode
{
    public const SIX_FOUR_ZERO = 600400;

    public static array $errMsg = [
        self::SIX_FOUR_ZERO => '部门不存在',
    ];

    public static array $succMsg = [
        'add' => '部门添加成功',
        'edit' => '部门编辑成功',
        'del' => '部门删除成功',
        'use' => '部门启用成功',
        'disable' => '部门禁用成功',
        'order' => '部门排序成功',
    ];
}
