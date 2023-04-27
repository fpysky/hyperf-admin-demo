<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DetailAction extends AbstractAction
{
    #[GetMapping(path: '{id:\d+}')]
    public function handle(int $id): ResponseInterface
    {
        $admin = Admin::query()
            ->with(['adminRole'])
            ->findOrFail($id);

        $data = [
            'id' => $admin->id,
            'deptId' => $admin->dept_id,
            'deptIds' => [$admin->dept_id],
            'email' => $admin->email,
            'logo' => $admin->logo,
            'lastLoginIp' => $admin->last_login_ip,
            'lastLoginTime' => $admin->last_login_time,
            'mobile' => $admin->mobile,
            'name' => $admin->name,
            'postId' => $admin->post_id,
            'roleIds' => $admin->roleIds(),
            'status' => $admin->status,
            'type' => $admin->type,
        ];

        return $this->success($data);
    }
}
