<?php

declare(strict_types=1);

namespace App\Actions\Setting\Taste;

use App\Actions\AbstractAction;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Taste;
use App\Request\Taste\StatusRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class StatusAction extends AbstractAction
{
    #[PatchMapping(path: '/taste/status')]
    #[Patch(path: '/taste/status', summary: '口味启用或禁用', tags: ['后台管理/页面设置/口味模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'status'],
        properties: [
            new Property(property: 'id', description: '场景id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态 0.禁用 1.启用', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(StatusRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $status = (int) $request->input('status');

        Taste::query()
            ->findOrFail($id)
            ->update([
                'status' => $status,
            ]);

        return $this->message();
    }
}
