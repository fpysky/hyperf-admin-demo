<?php

declare(strict_types=1);

namespace App\Actions\Area;

use App\Actions\AbstractAction;
use App\Dao\Area\AreaDao;
use App\Exception\UnprocessableEntityException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Parameter;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
class DetailAction extends AbstractAction
{
    #[Inject]
    protected AreaDao $areaDao;

    #[GetMapping(path: '/area/{id:\d+}')]
    #[Get(path: '/area/{id}', summary: '地区详情', tags: ['后台管理/地区管理'])]
    #[Parameter(name: 'id', description: '地区id', in: 'path', schema: new Schema('integer'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', properties: [
                new Property(property: 'id', description: '地区id', type: 'integer', example: 1),
                new Property(property: 'name', description: '地区名称', type: 'string', example: '滨海'),
                new Property(property: 'merchantNum', description: '商家数', type: 'integer', example: 1),
                new Property(property: 'sortOrder', description: '排序', type: 'integer', example: 1),
                new Property(property: 'createdAt', description: '创建时间(YYYY-MM-DD HH:ii)', type: 'string', example: '2023-04-01 22:23'),
            ], type: 'object'),
        ]
    ))]
    public function handle(int $id): ResponseInterface
    {
        if ($id === 0) {
            throw new UnprocessableEntityException('id错误');
        }

        $data = $this->areaDao->detail($id);
        return $this->success($data);
    }
}
