<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class])]
class UserInfoAction extends AbstractAction
{
    #[GetMapping(path: 'userInfo')]
    public function handle(): ResponseInterface
    {
        $admin = admin();

        if ($admin->isSuper()) {
            $roles = ['admin'];
            $menus = Rule::getSuperAdminMenus();
        } else {
            $roles = $admin->getRolesNames();
            $menus = $admin->menus();
        }

        return $this->success([
            'id' => $admin->getId(),
            'roles' => $roles,
            'routes' => $menus,
        ]);
    }
}
