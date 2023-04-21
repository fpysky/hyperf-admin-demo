<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class TokenCode extends CommonCode
{
    public const FO_ZE_O_ZE_ZE_O = 401001; // 账号或密码错误

    public const FO_ZE_O_ZE_ZE_TH = 401003; //	用户被禁用

    public const FO_ZE_O_ZE_ZE_FO = 401004; // 未授权或授权过期

    public const FO_ZE_O_ZE_ZE_FI = 401005; // 签名失败

    public static array $errMsg = [
        self::FO_ZE_O_ZE_ZE_O => '账号或密码错误',
        self::FO_ZE_O_ZE_ZE_TH => '用户已被禁用',
        self::FO_ZE_O_ZE_ZE_FO => '未授权或授权过期',
        self::FO_ZE_O_ZE_ZE_FI => '签名失败',
    ];

    public static array $succMsg = [
        'login' => '登录成功',
        'logout' => '退出登录成功',
        'del' => '管理员删除成功',
        'use' => '管理员启用成功',
        'disable' => '管理员禁用成功',
        'password' => '管理员修改密码成功',
    ];
}
