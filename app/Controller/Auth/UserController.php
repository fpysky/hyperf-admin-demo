<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Middleware\AuthMiddleware;
use App\Model\Rule;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class])]
class UserController extends AbstractController
{
    #[GetMapping(path: 'userInfo')]
    public function userInfo(): ResponseInterface
    {
        $admin = admin();

        if ($admin->isSuper()) {
            $roles = ['admin'];
        } else {
            $roles = $admin->getRolesNames();
        }

        return $this->success([
            'id' => $admin->getId(),
            'roles' => $roles,
            'permissions' => $admin->buttonPermissions(),
        ]);
    }

    #[GetMapping(path: 'userRouters')]
    public function userRouters(): ResponseInterface
    {
        $admin = admin();

        if ($admin->isSuper()) {
            $menus = Rule::getSuperAdminMenus();
        } else {
            $menus = $admin->menus();
        }

        return $this->success($menus);
    }
}
