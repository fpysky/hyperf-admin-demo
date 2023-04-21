<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class AdminCode extends CommonCode
{
    // SIX 600000 ONE 100 ZERO 0
    public const SIX_ONE_ZERO = 600100;

    public const SIX_ONE_FOUR = 600104;

    public const SIX_ONE_FIVE = 600105;

    public const SIX_ONE_SEVEN = 600107;

    public static array $errMsg = [
        self::SIX_ONE_ZERO => '管理员不存在',
        self::SIX_ONE_FOUR => '不能编辑超级管理员',
        self::SIX_ONE_FIVE => '不能删除超级管理员',
        self::SIX_ONE_SEVEN => '不能禁用超级管理员',
    ];

    public static array $succMsg = [
        'add' => '管理员添加成功',
        'edit' => '管理员编辑成功',
        'del' => '管理员删除成功',
        'use' => '管理员启用成功',
        'disable' => '管理员禁用成功',
        'password' => '管理员修改密码成功',
    ];
}
