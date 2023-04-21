<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Image;

use App\Actions\AbstractAction;
use App\Request\Merchant\Image\CreateRequest;
use App\Services\Merchant\MerchantImageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected MerchantImageService $imageService;

    /**
     * @api {post} /merchant/image  添加图片
     * @apiDescription 添加图片 yulu 2023/4/4
     * @apiName 添加图片
     * @apiGroup 商家管理-商家详情-图片列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} merchantId 商户id
     * @apiParam {string} imageUrl 图片url
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 视频id
     * @apiSuccess {integer} data.merchantId 商户id
     * @apiSuccess {string} data.imageUrl 图片url
     *
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *          "id": 100,
     *          "merchantId": 10,
     *          "imageUrl": "http://www.cdn.img/afaf1.jpg",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PostMapping(path: '/merchant/image')]
    public function handle(CreateRequest $request): ResponseInterface
    {
        $params = $request->all();
        $merchantId = (int) $params['merchantId'];
        $imageUrl = $params['imageUrl'];

        $id = $this->imageService->create($merchantId, $imageUrl);
        $data = [];
        $data['id'] = $id;
        $data['merchantId'] = $merchantId;
        $data['imageUrl'] = $imageUrl;
        return $this->success($data);
    }
}
