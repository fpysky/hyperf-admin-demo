<?php

declare(strict_types=1);

namespace App\Service;

use App\Extend\Log\Log;
use App\Model\Admin;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager;

class AuthService
{
    #[Inject]
    protected AuthManager $auth;

    public function generateAccessToken(Admin $admin): string
    {
        return $this->auth->guard('sso')->login($admin);
    }

    public function logout(): void
    {
        $this->auth->guard('sso')->logout();
    }

    public function batchLogoutAdmin(array $adminIds): void
    {
        try {
            $this->auth->batchLogoutByAdmin($adminIds);
        } catch (\Throwable $e) {
            Log::get()->error($e->getMessage());
        }
    }
}
