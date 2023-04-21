<?php

declare(strict_types=1);

namespace App\Actions\Area;

use App\Actions\AbstractAction;
use App\Dao\Area\AreaDao;
use App\Exception\UnprocessableEntityException;
use App\Request\Merchant\Tag\CreateRequest;
use App\Request\Merchant\Tag\UpdateRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
class UpdateAction extends AbstractAction
{
    #[Inject]
    protected AreaDao $areaDao;

    #[PutMapping(path: '/area')]
    #[Put(path: '/area', summary: '更新地区', tags: ['后台管理/地区管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'name', 'sortOrder'],
        properties: [
            new Property(property: 'id', description: '地区id', type: 'integer', example: 1),
            new Property(property: 'name', description: '地区名称', type: 'string', example: '海南'),
            new Property(property: 'sortOrder', description: '排序', type: 'integer', example: 1),
            new Property(property: 'pid', description: '父id', type: 'integer', example: 1),
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
        $name = (string) $request->input('name');
        $sortOrder = (int) $request->input('sortOrder');
        $pid = (int) $request->input('pid');
        if ($id === 0) {
            throw new UnprocessableEntityException('id错误');
        }

        // 判断地区是否已存在，同级地区不可相同
        if ($this->areaDao->existsByName($pid, $name)) {
            throw new UnprocessableEntityException('该地区已存在');
        }
        $this->areaDao->update($id, $pid, $name, $sortOrder);

        return $this->message('操作成功');
    }
}
