<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\RecordNotFoundException;
use App\Exception\UnprocessableEntityException;
use App\Request\LoginRequest;
use App\Service\AdminService;
use App\Service\AuthService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
class AuthController extends AbstractController
{
    #[Inject]
    protected AdminService $adminService;

    #[Inject]
    protected AuthService $authService;

    #[PostMapping(path: 'login')]
    public function login(LoginRequest $request): ResponseInterface
    {
        $mobile = $request->string('username');
        $password = $request->string('password');
        $ip = $request->getClientIp();

        try {
            $admin = $this->adminService->findByMobileOrFail($mobile);
        } catch (RecordNotFoundException) {
            throw new UnprocessableEntityException('账户不存在');
        }

        if ($admin->isDisabled()) {
            throw new UnprocessableEntityException('用户已被禁用');
        }

        if (! checkPassword($password, $admin->password)) {
            throw new UnprocessableEntityException('账号或密码错误');
        }

        $accessToken = $this->authService->generateAccessToken($admin);
        $admin->syncUpdateLastLoginInfo($ip);

        return $this->success([
            'id' => $admin->id,
            'token' => $accessToken,
            'name' => $admin->name,
            'logo' => $admin->getAdminLogo(),
        ]);
    }

    #[PostMapping(path: 'logout')]
    public function logout(): ResponseInterface
    {
        $this->authService->logout();

        return $this->message('退出登录成功');
    }
}
