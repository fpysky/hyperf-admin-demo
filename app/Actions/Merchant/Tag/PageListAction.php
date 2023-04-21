<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Tag;

use App\Actions\AbstractAction;
use App\Dao\Merchant\Tag\MerchantTagDao;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Swagger\Annotation\Get;
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
class PageListAction extends AbstractAction
{
    #[Inject]
    protected MerchantTagDao $merchantTagDao;

    #[GetMapping(path: '/merchant/tag/pagelist')]
    #[Get(path: '/merchant/tag/pagelist', summary: '服务标签列表（分页）', tags: ['后台管理/商家管理/服务标签管理'])]
    #[QueryParameter(name: 'page', description: '页码', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', schema: new Schema(type: 'integer'), example: 15)]
    #[QueryParameter(name: 'name', description: '服务标签名称', schema: new Schema(type: 'string'), example: '回头客常拼')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', properties: [
                new Property(property: 'total', description: '总条数', type: 'integer', example: 200),
                new Property(
                    property: 'list',
                    description: '服务标签列表',
                    type: 'array',
                    items: new Items(properties: [
                        new Property(property: 'id', description: '地区id', type: 'integer', example: 1),
                        new Property(property: 'name', description: '地区名称', type: 'string', example: '滨海'),
                        new Property(property: 'merchantNum', description: '商家数', type: 'integer', example: 1),
                        new Property(property: 'sortOrder', description: '排序', type: 'integer', example: 1),
                        new Property(property: 'status', description: '状态 1为启用，0为停用', type: 'integer', example: 1),
                        new Property(property: 'createdAt', description: '创建时间(YYYY-MM-DD HH:ii)', type: 'string', example: '2023-04-01 22:23'),
                    ]),
                ),
            ], type: 'object'),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $name = (string) $this->request->input('name');
        $page = (int) $this->request->input('page');
        $pageSize = (int) $this->request->input('pageSize');

        $total = $this->merchantTagDao->total($name);
        if ($total == 0) {
            $this->success(null);
        }

        $data = $this->merchantTagDao->pageList($name, $page, $pageSize);
        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }
}
