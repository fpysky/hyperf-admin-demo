<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class TopRuleAction extends AbstractAction
{
    #[GetMapping(path: '/rule/topRule')]
    public function handle(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->get();

        return $this->success($rules);
    }
}
