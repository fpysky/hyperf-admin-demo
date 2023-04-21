<?php

declare(strict_types=1);

namespace App\Event;

class AdminLogin
{
    // 建议这里定义成 public 属性，以便监听器对该属性的直接使用，或者你提供该属性的 Getter
    public $adminId;

    public $ip;

    public function __construct($adminId, $ip)
    {
        $this->adminId = $adminId;
        $this->ip = $ip;
    }
}
