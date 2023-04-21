<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Request\Merchant\Merchant\StatusRequest;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class StatusAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {put} /merchant/status 禁封商家
     * @apiDescription 禁封商家  yulu 2023/4/4
     * @apiName 禁封商家
     * @apiGroup 商家管理-商家列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} id 商家id
     * @apiParam {integer} status 状态   1解封 2封禁
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 商家id
     * @apiSuccess {integer} data.status 操作后的状态
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "操作成功",
     *    "data": {
     *          "id":100,
     *          "status":1,
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PutMapping(path: '/merchant/status')]
    public function handle(StatusRequest $request): ResponseInterface
    {
        $params = $request->all();
        $id = (int) $params['id'];
        $status = (int) $params['status'];
        $this->merchantService->updateStatus($id, $status);
        $data = [];
        $data['id'] = $id;
        $data['status'] = $status;
        return $this->success($data, '操作成功');
    }
}
