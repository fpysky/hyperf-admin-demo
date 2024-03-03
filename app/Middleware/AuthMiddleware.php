<?php

declare(strict_types=1);

namespace App\Middleware;

use Qbhy\HyperfAuth\AuthMiddleware as Base;

class AuthMiddleware extends Base
{
    protected array $guards = ['sso'];
}
