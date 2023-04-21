<?php

declare(strict_types=1);

namespace App\AdminRbac\CodeMsg;

class CommonCode
{
    // http status code
    public const STATUS_CODE_OK = 200; // 请求成功

    public const FOUR_HUNDRED = 400; // 参数错误（缺失参数）

    public const FOUR_HUNDRED_ONE = 401; // Unauthorized

    public const FOUR_HUNDRED_THREE = 403; // 禁止访问

    public const FOUR_HUNDRED_FOUR = 404; // 请求的数据不存在

    public const FOUR_HUNDRED_TWENTY_TWO = 422; // 参数内部错误：xxx 参数内容只能是数字，请修正

    public const STATUS_CODE_SERVER_ERROR = 500;

    // 业务code
    public const OK = 200000; // 成功code

    public const SERVER_ERROR = 500000;

    public const FO_ZE_ZE_ZE_ZE_O = 400001; // 缺失参数

    public const FO_ZE_TH_ZE_ZE_O = 403001; // 无操作权限

    public const FO_TW_TW_ZE_ZE_ZE = 422000; // 参数内容错误

    public static array $errMsg = [
        self::FO_ZE_ZE_ZE_ZE_O => '缺失参数',
        self::FO_ZE_TH_ZE_ZE_O => '无操作权限',
        self::FO_TW_TW_ZE_ZE_ZE => '参数错误',
    ];

    public static array $succMsg = [
        self::OK => 'ok',
    ];
}
