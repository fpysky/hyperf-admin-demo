<?php

namespace App\Actions\Merchant\Category;

use App\Actions\AbstractAction;
use App\Request\Merchant\Category\ListRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use App\Services\Merchant\MerchantCategoryService;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ListAction extends AbstractAction
{
    #[Inject]
    protected MerchantCategoryService $categoryService;

    /**
     * @api {get} /merchant/categories  分类搜索列表
     * @apiDescription 分类搜索列表  yulu 2023/4/4
     * @apiName 分类搜索列表
     * @apiGroup 商家管理-商家分类
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {int} [page]  页码 默认1
     * @apiParam {int} [pageSize] 每页条数  默认10
     * @apiParam {string} [categoryName] 分类名称
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {int} data.total  总记录条数
     * @apiSuccess {array} data.list 响应内容
     * @apiSuccess {int} data.list.id 分类id
     * @apiSuccess {int} data.list.parentId 父分类id
     * @apiSuccess {string} data.list.path 从顶级分类到当前的分类名称  中间用,分割  例如  美食,快餐,炸鸡
     * @apiSuccess {string} data.list.categoryName 分类名称
     * @apiSuccess {string} data.list.iconUrl 分类图标地址
     * @apiSuccess {int} data.list.merchantCount 商家数量
     * @apiSuccess {int} data.list.sortOrder 排序
     * @apiSuccess {string} data.list.createdAt 创建时间
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *              "total": 10,
     *              "list": [{
     *                  "id": 12,
     *                  "parentId": 10,
     *                  "path": "美食,快餐,炸鸡",
     *                  "categoryName": "炸鸡",
     *                  "iconUrl": "http://222.werwr.com/1.jpg",
     *                  "merchantCount": 500,
     *                  "sortOrder": 10,
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
    #[GetMapping(path: '/merchant/categories')]
    public function handle(ListRequest $request): ResponseInterface
    {
        $params = $request->all();

        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $pageSize = isset($params['pageSize']) ? (int)$params['pageSize'] : 10;
        $categoryName = isset($params['categoryName']) ? (string)$params['categoryName'] : '';

        $data = $this->categoryService->searchPageList($page,$pageSize,$categoryName);
        return $this->success($data);
    }
}
