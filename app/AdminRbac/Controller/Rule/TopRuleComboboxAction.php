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
class TopRuleComboboxAction extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminRule/topRuleCombobox')]
    public function handle(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name as label'])
            ->where('parent_id', 0)
            ->get();

        return $this->success($rules);
    }
}
