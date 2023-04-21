<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class DetailAction extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminRule/{id:\d+}')]
    public function handle(int $id): ResponseInterface
    {
        $rule = Rule::query()
            ->select([
                'id', 'parent_id as parentId', 'status',
                'type', 'order as sort', 'name',
                'icon', 'route', 'path',
            ])
            ->findOrFail($id);

        return $this->success($rule);
    }
}
