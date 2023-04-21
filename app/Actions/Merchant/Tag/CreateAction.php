<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Tag;

use App\Actions\AbstractAction;
use App\Dao\Merchant\Tag\MerchantTagDao;
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
    protected MerchantTagDao $merchantTagDao;

    #[PostMapping(path: '/merchant/tag')]
    #[Post(path: '/merchant/tag', summary: '新增服务标签', tags: ['后台管理/商家管理/服务标签管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['name'],
        properties: [
            new Property(property: 'name', description: '名称(最多8个字)', type: 'string', example: '回头客常拼'),
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

        // 判断标签是否已存在
        if ($this->merchantTagDao->existsByName($name)) {
            throw new UnprocessableEntityException('该标签已存在');
        }

        $this->merchantTagDao->create($name);
        return $this->message('操作成功');
    }
}
