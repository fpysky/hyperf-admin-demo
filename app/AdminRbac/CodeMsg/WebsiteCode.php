<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class WebsiteCode extends CommonCode
{
    public const SIX_SIX_ZERO = 600600;

    public static array $errMsg = [
        self::SIX_SIX_ZERO => '站点不存在',
    ];

    public static array $succMsg = [
        'save' => '站点保存成功',
    ];
}
