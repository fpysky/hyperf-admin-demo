<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Dept;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Resource\Dept\DeptSelectData;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class SelectDataAction extends AbstractAction
{
    #[GetMapping(path: '/dept/selectData')]
    public function handle(): ResponseInterface
    {
        $list = Dept::query()
            ->with([
                'enabledChildren' => function (HasMany $query) {
                    $query->select(['id', 'name', 'parent_id'])
                        ->orderBy('order')
                        ->orderByDesc('id');
                },
            ])
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->where('status', Dept::STATUS_ENABLE)
            ->orderBy('order')
            ->orderByDesc('id')
            ->get();

        return $this->success(DeptSelectData::collection($list));
    }
}
