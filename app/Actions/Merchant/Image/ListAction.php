<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Image;

use App\Actions\AbstractAction;
use App\Request\Merchant\Image\ListRequest;
use App\Services\Merchant\MerchantImageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ListAction extends AbstractAction
{
    #[Inject]
    protected MerchantImageService $imageService;

    /**
     * @api {get} /merchant/images  图片列表
     * @apiDescription 图片列表  yulu 2023/4/4
     * @apiName 图片列表
     * @apiGroup 商家管理-商家详情-图片列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} merchantId 商户id
     * @apiParam {integer} [page]  页码 默认1
     * @apiParam {integer} [pageSize] 每页条数  默认10
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.total  总记录条数
     * @apiSuccess {array} data.list 响应内容
     * @apiSuccess {integer} data.list.id 视频id
     * @apiSuccess {string} data.list.imageUrl 封片图片url
     * @apiSuccess {string} data.list.createdAt 创建时间
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *              "total": 10,
     *              "list": [{
     *                  "id": 12,
     *                  "imageUrl": "http://222.werwr.com/1.jpg",
     *                  "createAt": "2022-02-02 12:00",
     *          }]
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[GetMapping(path: '/merchant/images')]
    public function handle(ListRequest $request): ResponseInterface
    {
        $params = $request->all();

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $pageSize = isset($params['pageSize']) ? (int) $params['pageSize'] : 10;
        $merchantId = (int) $params['merchantId'];
        $data = $this->imageService->searchPageList($page, $pageSize, $merchantId);
        return $this->success($data);
    }
}
