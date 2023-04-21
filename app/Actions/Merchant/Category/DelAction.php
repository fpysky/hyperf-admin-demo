<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Category;

use App\Actions\AbstractAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use App\Services\Merchant\MerchantCategoryService;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class DelAction extends AbstractAction
{
    #[Inject]
    protected MerchantCategoryService $categoryService;

    /**
     * @api {delete} /merchant/category  删除分类
     * @apiDescription 删除分类 yulu 2023/4/4
     * @apiName 删除分类
     * @apiGroup 商家管理-商家分类
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {int} id 分类id
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {int} data.id 分类id
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
    #[DeleteMapping(path: '/merchant/category/{id}')]
    public function handle(int $id): ResponseInterface
    {
        $this->categoryService->delete($id);
        return $this->success(['id'=>$id]);
    }
}

