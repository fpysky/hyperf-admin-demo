<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Category;

use App\Actions\AbstractAction;
use App\Request\Merchant\Category\CreateRequest;
use App\Services\Merchant\MerchantCategoryService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected MerchantCategoryService $categoryService;

    /**
     * @api {post} /merchant/category  创建分类
     * @apiDescription 创建分类 yulu 2023/4/4
     * @apiName 创建分类
     * @apiGroup 商家管理-商家分类
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {int} parentId 父分类id   必须传 0表示顶级分类
     * @apiParam {string} categoryName 分类名称
     * @apiParam {string} iconUrl 分类图标地址
     * @apiParam {int} sortOrder 排序
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {int} data.id 商户id
     * @apiSuccess {int} data.parentId 父分类id
     * @apiSuccess {string} data.categoryName 分类名称
     * @apiSuccess {string} data.iconUrl 分类图标地址
     * @apiSuccess {string} data.categoryTxt 商家分类文字 多级使用>分隔  比如 美食>下午茶
     *
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *          "id": 100,
     *          "parentId": 10,
     *          "categoryName": "下午茶",
     *          "iconUrl": "http://www.cdn.img/afaf1.jpg",
     *          "categoryTxt": "美食>下午茶",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PostMapping(path: '/merchant/category')]
    public function handle(CreateRequest $request): ResponseInterface
    {
        $params = $request->all();
        $parentId = (int) $params['parentId'];
        $categoryName = $params['categoryName'];
        $iconUrl = $params['iconUrl'];
        $sortOrder = (int) $params['sortOrder'];
        $data = [];
        $res = $this->categoryService->create($parentId, $categoryName, $iconUrl, $sortOrder);
        $data['id'] = $res['id'];
        $data['parentId'] = $parentId;
        $data['categoryName'] = $categoryName;
        $data['iconUrl'] = $iconUrl;
        // todo 分类名称 从父到子
        $data['categoryTxt'] = $res['path'];
        return $this->success($data);
    }
}
