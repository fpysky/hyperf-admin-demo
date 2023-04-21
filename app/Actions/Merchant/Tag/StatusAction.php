<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Tag;

use App\Actions\AbstractAction;
use App\Dao\Merchant\Tag\MerchantTagDao;
use App\Exception\UnprocessableEntityException;
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
class StatusAction extends AbstractAction
{
    #[Inject]
    protected MerchantTagDao $merchantTagDao;

    #[PutMapping(path: '/merchant/tag')]
    #[Put(path: '/merchant/tag', summary: '更新服务标签状态', tags: ['后台管理/商家管理/服务标签管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'status'],
        properties: [
            new Property(property: 'id', description: '地区id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态 1为启用，0为停用', type: 'integer', example: 1),
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
        $status = (int) $request->input('status');
        if ($id === 0) {
            throw new UnprocessableEntityException('id错误');
        }

        var_dump($status);

        $this->merchantTagDao->updateStatus($id, $status);
        return $this->message('操作成功');
    }
}
