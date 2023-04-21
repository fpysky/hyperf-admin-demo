<?php

declare(strict_types=1);

namespace App\Extend\Auth;

use App\Extend\Redis\DefaultRedis;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager as Base;
use Qbhy\HyperfAuth\Guard\SsoGuard;

class AuthManager extends Base
{
    #[Inject]
    protected DefaultRedis $redis;

    public function logoutByAdminId(int $adminId)
    {
        $tokenKey = config('auth.guards.sso.redis_key');
        $tokenKey = str_replace('{uid}', (string) $adminId, $tokenKey);
        $token = $this->redis->hGet($tokenKey,'web');

        if (is_string($token) && ! empty($token)) {
            /** @var SsoGuard $guard */
            $guard = $this->guard('sso');
            $jwt = $guard->getJwtManager()->parse($token);
            $guard->getJwtManager()->addBlacklist($jwt);
        }
    }
}
