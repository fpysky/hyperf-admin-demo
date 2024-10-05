<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\AdminOperationLogResource;
use App\Service\OperateLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class SystemController extends AbstractController
{
    #[Inject]
    protected OperateLogService $operateLogService;

    #[GetMapping(path: 'system/operateLog')]
    public function operateLogList(): ResponseInterface
    {
        $pageSize = $this->request->getPageSize();
        $searchOptions = [
            'operateType' => $this->request->integer('operateType'),
            'module' => $this->request->string('module'),
            'operateAdmin' => $this->request->string('operateAdmin'),
            'operateStatusStr' => $this->request->string('operateStatusStr'),
            'operateTimeStart' => $this->request->string('operateTimeStart'),
            'operateTimeEnd' => $this->request->string('operateTimeEnd'),
        ];

        $paginator = $this->operateLogService->getPaginateList($pageSize, $searchOptions);

        return $this->success([
            'list' => AdminOperationLogResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }
}
