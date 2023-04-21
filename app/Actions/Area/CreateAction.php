<?php

declare(strict_types=1);

namespace App\Actions\Area;

use App\Actions\AbstractAction;
use App\Dao\Area\AreaDao;
use App\Exception\UnprocessableEntityException;
use App\Request\Merchant\Tag\CreateRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected AreaDao $areaDao;

    #[PostMapping(path: '/area')]
    #[Post(path: '/area', summary: '新增地区', tags: ['后台管理/地区管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['name', 'sortOrder'],
        properties: [
            new Property(property: 'name', description: '名称(最多8个字)', type: 'string', example: '海南'),
            new Property(property: 'sortOrder', description: '排序', type: 'integer', example: 1),
            new Property(property: 'pid', description: '父id', type: 'integer', example: 1),
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
    public function handle(CreateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $sortOrder = (int) $request->input('sortOrder');
        $pid = (int) $request->input('pid', 0);

        // 判断地区是否已存在，同级地区不可相同
        if ($this->areaDao->existsByName($pid, $name)) {
            throw new UnprocessableEntityException('该地区已存在');
        }

        $this->areaDao->create($pid, $name, $sortOrder);
        return $this->message('操作成功');
    }
}
