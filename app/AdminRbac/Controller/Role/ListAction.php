<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'role')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ListAction extends AbstractAction
{
    #[GetMapping(path: '')]
    public function handle(): ResponseInterface
    {
        $roles = Role::query()
            ->select([
                'id', 'name', 'desc as remark',
                'created_at as createTime',
                'order as sort', 'status',
            ])
            ->with([
                'roleRule' => function ($query) {
                    $query->with('rule');
                },
            ])
            ->orderBy('order')
            ->orderByDesc('id')
            ->get()
            ->toArray();

        return $this->success($roles);
    }
}
