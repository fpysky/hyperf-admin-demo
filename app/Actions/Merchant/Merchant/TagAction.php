<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Request\Merchant\Merchant\TagRequest;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class TagAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {put} /merchant/addTag 配置标签
     * @apiDescription 配置标签  yulu 2023/4/4
     * @apiName 配置标签
     * @apiGroup 商家管理-商家列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} id 商家id
     * @apiParam {string} tagId 标签id 多个用,分割
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {string} data.tagId 标签id 多个用,分割
     * @apiSuccess {string} data.tagTxt 标签名称 多个用,分割
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "操作成功",
     *    "data": {
     *          "id":100,
     *          "tagId":"1,2,3",
     *          "tagName":"有趣,有料,有种",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PutMapping(path: '/merchant/addTag')]
    public function handle(TagRequest $request): ResponseInterface
    {
        $params = $request->all();
        $id = (int) $params['id'];
        $tagId = $params['tagId'];
        $res = $this->merchantService->addTag($id, $tagId);
        if ($res) {
            return $this->success($res);
        }
        return $this->error('操作失败', ErrorCode::SERVER_ERROR);
    }
}
