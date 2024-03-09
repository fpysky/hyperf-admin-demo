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
class UserRoutersAction extends AbstractAction
{
    #[GetMapping(path: 'userRouters')]
    public function handle(): ResponseInterface
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
