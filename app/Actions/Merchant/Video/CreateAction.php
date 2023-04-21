<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Video;

use App\Actions\AbstractAction;
use App\Request\Merchant\Video\CreateRequest;
use App\Services\Merchant\MerchantVideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected MerchantVideoService $videoService;
    /**
     * @api {post} /merchant/video  添加视频
     * @apiDescription 添加视频 yulu 2023/4/4
     * @apiName 添加视频
     * @apiGroup 商家管理-商家详情-视频列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} merchantId 商户id
     * @apiParam {string} title 视频标题
     * @apiParam {string} videoUrl 视频url
     * @apiParam {string} coverUrl 缩略图url
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 视频id
     * @apiSuccess {integer} data.merchantId 商户id
     * @apiSuccess {string} data.title 视频标题
     * @apiSuccess {string} data.videoUrl 视频url
     * @apiSuccess {string} data.coverUrl 缩略图url
     *
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",     *    "data": {
     *          "id": 100,
     *          "merchantId": 10,
     *          "title": "下午茶",
     *          "videoUrl": "http://www.cdn.img/afaf1.mp4",
     *          "coverUrl": "http://www.cdn.img/afaf1.jpg",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PostMapping(path: '/merchant/video')]
    public function handle(CreateRequest $request): ResponseInterface
    {
        $params = $request->all();
        $merchantId = (int) $params['merchantId'];
        $title = $params['title'];
        $videoUrl = $params['videoUrl'];
        $coverUrl = $params['coverUrl'];
        $id = $this->videoService->create($merchantId, $title, $videoUrl, $coverUrl);
        $data = [];
        $data['id'] = $id;
        $data['merchantId'] = $merchantId;
        $data['title'] = $title;
        $data['videoUrl'] = $videoUrl;
        $data['coverUrl'] = $coverUrl;
        return $this->success($data);
    }
}
