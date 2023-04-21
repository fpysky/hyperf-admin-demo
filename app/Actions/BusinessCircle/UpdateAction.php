<?php

declare(strict_types=1);

namespace App\Actions\BusinessCircle;

use App\Actions\AbstractAction;
use App\Dao\BusinessCircle\BusinessCircleDao;
use App\Middleware\AuthMiddleware;
use App\Request\BusinessCircle\UpdateRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class])]
class UpdateAction extends AbstractAction
{
    #[Inject]
    protected BusinessCircleDao $businessCircleDao;

    #[PutMapping(path: '/businessCircle')]
    #[Put(path: '/businessCircle', summary: '更新商圈', tags: ['后台管理/商圈管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'areaId', 'name', 'sort'],
        properties: [
            new Property(property: 'id', description: '商圈id', type: 'integer', example: 1),
            new Property(property: 'areaId', description: '地区id', type: 'integer', example: 1),
            new Property(property: 'name', description: '商圈名称', type: 'string', example: '龙华区'),
            new Property(property: 'sort', description: '地区id', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '更新成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(UpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $areaId = (int) $request->input('areaId');
        $name = (string) $request->input('name');
        $sort = (int) $request->input('sort');

        $this->businessCircleDao->update($id, $areaId, $name, $sort);

        return $this->message('更新成功');
    }
}
