<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class RoleCode extends CommonCode
{
    public const SIX_TWO_ZERO = 600200;

    public static array $errMsg = [
        self::SIX_TWO_ZERO => '角色不存在',
    ];

    public static array $succMsg = [
        'add' => '角色添加成功',
        'edit' => '角色编辑成功',
        'del' => '角色删除成功',
        'use' => '角色启用成功',
        'disable' => '角色禁用成功',
        'order' => '角色排序成功',
        'rule' => '权限分配成功',
    ];
}
