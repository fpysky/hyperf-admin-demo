<?php

declare(strict_types=1);

namespace App\Extend\Redis;

use Hyperf\Redis\Redis;

class DefaultRedis extends Redis
{
    protected string $poolName = 'default';
}
