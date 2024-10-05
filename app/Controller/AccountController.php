<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Admin;
use App\Request\Account\ChangePasswordRequest;
use App\Service\AdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class AccountController extends AbstractController
{
    #[Inject]
    protected AdminService $adminService;

    #[PostMapping(path: 'account/changePassword')]
    public function changePassword(ChangePasswordRequest $request): ResponseInterface
    {
        $id = $request->integer('id');
        $password = $request->string('password');
        $newPassword = $request->string('newPassword');

        $admin = Admin::findFromCacheOrFail($id);

        if (! password_verify($password, $admin->password)) {
            throw new UnprocessableEntityException('原密码错误');
        }

        if (password_verify($newPassword, $admin->password)) {
            throw new UnprocessableEntityException('原密码与修改密码相同');
        }

        $this->adminService->changePassword($admin, $newPassword);

        return $this->message('密码修改成功');
    }
}
