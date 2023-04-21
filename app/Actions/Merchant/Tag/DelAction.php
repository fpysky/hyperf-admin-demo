<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Tag;

use App\Actions\AbstractAction;
use App\Dao\Merchant\Tag\MerchantTagDao;
use App\Exception\UnprocessableEntityException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Parameter;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
class DelAction extends AbstractAction
{
    #[Inject]
    protected MerchantTagDao $merchantTagDao;

    #[DeleteMapping(path: '/merchant/tag/{id:\d+}')]
    #[Delete(path: '/merchant/tag/{id}', summary: '删除服务标签', tags: ['后台管理/商家管理/服务标签管理'])]
    #[Parameter(name: 'id', description: '服务标签id', in: 'path', schema: new Schema('integer'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '操作成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(int $id): ResponseInterface
    {
        if ($id === 0) {
            throw new UnprocessableEntityException('id错误');
        }

        $this->merchantTagDao->del($id);
        return $this->message('操作成功');
    }
}
