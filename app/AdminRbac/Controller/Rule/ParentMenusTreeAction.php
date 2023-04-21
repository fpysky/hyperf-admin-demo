<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class ParentMenusTreeAction extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminRule/parentMenusTree')]
    public function handle(): ResponseInterface
    {
        $rules = Rule::query()
            ->with([
                'children' => function (HasMany $query) {
                    $query->select(['id', 'name', 'parent_id']);
                },
            ])
            ->select(['id', 'name'])
            ->where('type', 1)
            ->get();

        return $this->success($rules);
    }
}
