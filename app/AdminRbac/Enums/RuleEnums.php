<?php

declare(strict_types=1);

namespace App\AdminRbac\Enums;

class RuleEnums
{
    public const DIRECTORY_TYPE = 1; // 目录

    public const MENU_TYPE = 2; // 菜单

    public const BUTTON_TYPE = 3; // 按钮

    public const API_TYPE = 4; // 接口

    public const USE = 1; // 启用

    public const DISABLE = 2; // 停用
}
