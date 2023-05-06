<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Dept;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\Dept\DeptResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ListAction extends AbstractAction
{
    #[GetMapping(path: '/dept')]
    public function handle(): ResponseInterface
    {
        $list = Dept::query()
            ->with(['children'])
            ->where('parent_id', 0)
            ->orderBy('sort')
            ->get();

        return $this->success(DeptResource::collection($list));
    }
}
