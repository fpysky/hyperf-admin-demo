<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Video;

use App\Actions\AbstractAction;
use App\Services\Merchant\MerchantVideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class DelAction extends AbstractAction
{
    #[Inject]
    protected MerchantVideoService $videoService;

    /**
     * @api {delete} /merchant/video/{id}  删除视频
     * @apiDescription 删除视频 yulu 2023/4/4
     * @apiName 删除视频
     * @apiGroup 商家管理-商家详情-视频列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} id 视频id
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 视频id
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "操作成功",
     *    "data": {
     *          "id":100,
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[DeleteMapping(path: '/merchant/video/{id}')]
    public function handle(int $id): ResponseInterface
    {
        $this->videoService->delete($id);
        return $this->success(['id' => $id]);
    }
}
