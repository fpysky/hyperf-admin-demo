<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Request\Merchant\Merchant\ListRequest;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ListAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {get} /merchant/merchants  商家列表
     * @apiDescription 商家列表  yulu 2023/4/4
     * @apiName 商家列表
     * @apiGroup 商家管理-商家列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} [page]  页码 默认1
     * @apiParam {integer} [pageSize] 每页条数  默认10
     * @apiParam {string} [categoryId] 分类id  持多选,多个用,分割  例如 1,2
     * @apiParam {string} [merchantName] 商家名称
     * @apiParam {string} [areaId] 地区id 支持多选,多个用,分割  例如 1,2
     * @apiParam {string} [businessCircleId] 商圈id  支持多选,多个用,分割  例如 1,2
     * @apiParam {string} [status] 状态:  默认全部  1-正常 2-禁用   支持多选,多个用,分割  例如 1,2
     * @apiParam {integer} [minSalesCount] 最小销量
     * @apiParam {integer} [maxSalesCount] 最大销量
     * @apiParam {string} [startCreateTime] 入驻开始时间 2022-09-02
     * @apiParam {string} [endCreateTime] 入驻结束时间 2022-09-02
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.total  总记录条数
     * @apiSuccess {array} data.list 响应内容
     * @apiSuccess {integer} data.list.id 商户id
     * @apiSuccess {string} data.list.categoryId 商家分类ID  多个用,分割
     * @apiSuccess {string} data.list.categoryTxt 分类文字   从一级到当前分类的文字显示,格式如下  多个用，分割  一级到下级分类用>分割,例如  快餐>小吃,美食>中餐>面食
     * @apiSuccess {string} data.list.merchantName 商家名称
     * @apiSuccess {integer} data.list.goodsCount 商品数量
     * @apiSuccess {integer} data.list.salesCount 销量
     * @apiSuccess {string} data.list.address 商家地址
     * @apiSuccess {integer} data.list.status 商家状态   0-全部 1-正常 2-禁用
     * @apiSuccess {string} data.list.createAt 入驻时间  2022-02-02 12:00
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *              "total": 10,
     *              "list": [{
     *                  "id": 100,
     *                  "categoryId": “12,13",
     *                  "categoryTxt": "快餐>小吃,美食>中餐>面食",
     *                  "merchantName": "龙华小明商家",
     *                  "goodsCount": 200,
     *                  "salesCount": 500,
     *                  "address": "海口市龙华区滨海大道100号",
     *                  "status": 1,
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
    #[GetMapping(path: '/merchant/merchants')]
    public function handle(ListRequest $request): ResponseInterface
    {
        $params = $request->all();

        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $pageSize = isset($params['pageSize']) ? (int)$params['pageSize'] : 10;
        $categoryId = isset($params['categoryId']) ? (string)$params['categoryId'] : '';
        $merchantName = isset($params['merchantName']) ? (string)$params['merchantName'] : '';
        $areaId = isset($params['areaId']) ? (string)$params['areaId'] : '';
        $businessCircleId = isset($params['businessCircleId']) ? (string)$params['businessCircleId'] : '';
        $status = isset($params['status']) ? (string)$params['status'] : '0';
        $minSalesCount = isset($params['minSalesCount']) ? (int)$params['minSalesCount'] : -1;
        $maxSalesCount = isset($params['maxSalesCount']) ? (int)$params['maxSalesCount'] : -1;
        $startCreateTime = isset($params['startCreateTime']) ? (string)$params['startCreateTime'] : '';
        $endCreateTime = isset($params['endCreateTime']) ? (string)$params['endCreateTime'] : '';

        $data = $this->merchantService->searchUserPageList(
            $page,
            $pageSize,
            $categoryId,
            $merchantName,
            $areaId,
            $businessCircleId,
            $status,
            $minSalesCount,
            $maxSalesCount,
            $startCreateTime,
            $endCreateTime
        );
        //test code
        $this->merchantService->getMerchantCateInfoList([1,35,22,55,37,41]);
        return $this->success($data);
    }
}
