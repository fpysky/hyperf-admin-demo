<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Rule;
use App\Resource\AdminMenusResource;
use App\Utils\Help;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'manager')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ManagerController extends AbstractController
{
    #[Inject]
    protected Help $help;

    #[GetMapping(path: 'system/backend/backendAdminRule/menus')]
    public function menus(): ResponseInterface
    {
        $admin = admin();

        if ($admin->isSuper()) {
            $menus = Rule::getSuperAdminMenus();
        } else {
            $menus = $admin->menus();
        }

        return $this->success(AdminMenusResource::collection($menus));
    }
}
