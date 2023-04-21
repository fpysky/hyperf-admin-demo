<?php

declare(strict_types=1);

namespace App\Actions\BusinessCircle;

use App\Actions\AbstractAction;
use App\Middleware\AuthMiddleware;
use App\Model\BusinessCircle\BusinessCircle;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class])]
class SelectDataAction extends AbstractAction
{
    #[GetMapping(path: '/businessCircle/selectData')]
    #[Get(path: '/businessCircle/selectData', summary: '商圈下拉组建数据', tags: ['后台管理/商圈管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'areaIds', description: '地区id数组字符串，英文逗号分隔', schema: new Schema(type: 'string'), example: '1,2')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['id', 'name'],
                type: 'array',
                items: new Items(properties: [
                    new Property(property: 'id', description: '商圈id', type: 'integer', example: 1),
                    new Property(property: 'name', description: '商圈名称', type: 'string', example: '滨海国际商圈'),
                ])
            ),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $areaIds = (string) $this->request->input('areaIds');
        $areaIds = explode(',', $areaIds) ?? [];

        $builder = BusinessCircle::query()
            ->select(['id', 'name']);

        if (! empty($areaIds)) {
            $builder->whereIn('area_id', $areaIds);
        }

        return $this->success($builder->get());
    }
}
