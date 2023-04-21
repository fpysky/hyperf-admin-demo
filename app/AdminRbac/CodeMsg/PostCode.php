<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class PostCode extends CommonCode
{
    public const SIX_FIVE_ZERO = 600500;

    public static array $errMsg = [
        self::SIX_FIVE_ZERO => '岗位不存在',
    ];

    public static array $succMsg = [
        'add' => '岗位添加成功',
        'edit' => '岗位编辑成功',
        'del' => '岗位删除成功',
        'use' => '岗位启用成功',
        'disable' => '岗位禁用成功',
        'order' => '岗位排序成功',
    ];
}
