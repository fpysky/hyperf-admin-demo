<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class RuleCode extends CommonCode
{
    public const SIX_THREE_ZERO = 600300;

    public static array $errMsg = [
        self::SIX_THREE_ZERO => '权限不存在',
    ];

    public static array $succMsg = [
        'add' => '权限添加成功',
        'edit' => '权限编辑成功',
        'del' => '权限删除成功',
        'use' => '权限启用成功',
        'disable' => '权限禁用成功',
        'order' => '权限排序成功',
    ];
}
