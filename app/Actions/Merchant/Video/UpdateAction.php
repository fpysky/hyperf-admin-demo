<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Video;

use App\Actions\AbstractAction;
use App\Request\Merchant\Video\UpdateRequest;
use App\Services\Merchant\MerchantVideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class UpdateAction extends AbstractAction
{
    #[Inject]
    protected MerchantVideoService $videoService;

    /**
     * @api {put} /merchant/video  编辑标题
     * @apiDescription 编辑标题 yulu 2023/4/4
     * @apiName 编辑标题
     * @apiGroup 商家管理-商家详情-视频列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} id  视频id
     * @apiParam {string} title 视频标题
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 商户id
     * @apiSuccess {string} data.title 视频标题
     *
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *          "id": 100,
     *          "title": "下午茶",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PutMapping(path: '/merchant/video')]
    public function handle(UpdateRequest $request): ResponseInterface
    {
        $params = $request->all();
        $id = (int) $params['id'];
        $title = $params['title'];
        $this->videoService->editTitle($id, $title);
        return $this->success(['id' => $id, 'title' => $title]);
    }
}
