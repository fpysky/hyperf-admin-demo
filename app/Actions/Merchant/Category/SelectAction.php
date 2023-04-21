<?php

namespace App\Actions\Merchant\Category;

use App\Actions\AbstractAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use App\Services\Merchant\MerchantCategoryService;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class SelectAction extends AbstractAction
{
    #[Inject]
    protected MerchantCategoryService $categoryService;
    /**
     * @api {get} /merchant/categorySelect  分类选择列表
     * @apiDescription 分类选择列表 yulu 2023/4/4
     * @apiName 分类选择列表
     * @apiGroup 商家管理-商家分类
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {array} data 一级分类列表
     * @apiSuccess {int} data.id 一级分类id
     * @apiSuccess {string} data.name 一级分类名称
     * @apiSuccess {array} data.list 二级分类列表
     * @apiSuccess {int} data.list.id 二级分类id
     * @apiSuccess {string} data.list.name 二级级分类名称
     * @apiSuccess {array} data.list.list 三级分类列表
     * @apiSuccess {int} data.list.list.id  三级分类id
     * @apiSuccess {string} data.list.list.name  三级分类名称
     * @apiSuccessExample {json}
     *  {
     *      "code": "200000",
     *      "msg": "",
     *      "data": [{
     *              "id": 100,
     *              "name": "美食",
     *              "list": [{
     *                      "id": 101,
     *                       "name": "快餐",
     *                      "list": [{
     *                          "id": 102,
     *                          "name": "炸鸡"
     *                              }]
     *                      }]
     *                  }]
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[GetMapping(path: '/merchant/categorySelect')]
    public function handle():ResponseInterface
    {
        $data = $this->categoryService->selectList();
        return $this->success($data);
    }
}
