<?php

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class DetailAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {get} /merchant/detail/{id} 商家展示
     * @apiDescription 商家展示  yulu 2023/4/4
     * @apiName 商家展示
     * @apiGroup 商家管理-商家列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {integer} id 商家id
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 商户id
     * @apiSuccess {string} data.phone 商家账户 11位手机号
     * @apiSuccess {string} data.merchantName 商家名称
     * @apiSuccess {integer} data.status 1正常 2封禁
     * @apiSuccess {integer} data.categoryId 商家分类ID  多个用,分割
     * @apiSuccess {string} data.categoryTxt  商家分类文字   从一级到当前分类的文字显示,格式如下  多个用，分割  一级到下级分类用>分割,例如  快餐>小吃,美食>中餐>面食
     * @apiSuccess {string} data.tagId  标签id 多个用,分割
     * @apiSuccess {string} data.tagTxt 标签名称  个用,分割
     * @apiSuccess {string} data.coverUrl 商家封面地址
     * @apiSuccess {string} data.recommendation 推荐语
     * @apiSuccess {string} data.videoUrl 宣传视频地址
     * @apiSuccess {string} data.imageUrls  宣传图片地址 多个,分割
     * @apiSuccess {integer} data.businessHourType  0/1/2
     * @apiSuccess {string} data.businessHours 营业时间 json格式
     * @apiSuccess {string} data.contact 联系方式 多个用,分割
     * @apiSuccess {integer} data.businessCircleId 所属商圈ID
     * @apiSuccess {string} data.businessCircle 所属商圈
     * @apiSuccess {string} data.address 详细地址
     * @apiSuccess {string} data.lnglat 定位(格式:lng,lat)
     * @apiSuccess {integer} data.goodsCount 在架商品数
     * @apiSuccess {integer} data.salesCount 累计销售商品数
     * @apiSuccess {integer} data.orderCount 累计订单数
     * @apiSuccess {float} data.salesPrice 核销金额 单位元,2位小数
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *          "id": 100,
     *          "phone": "13012345678",
     *          "merchantName": "龙华小茗商店",
     *          "status": 1,
     *          "categoryId": "12,13",
     *          "categoryTxt": "快餐>小吃,美食>中餐>面食",
     *          "tagId": "100,102",
     *          "tagTxt": "美食,标签2",
     *          "coverUrl": "http://www.cdn.img/afaf1.jpg",
     *          "recommendation": "最好的小吃店",
     *          "videoUrl": "http://www.cdn.img/afaf1.mp4",
     *          "imageUrls": "http://www.cdn.img/afaf1.jpg,http://www.cdn.img/afaf2.jpg",
     *          "businessHourType:0,
     *          "businessHours "[]",
     *          "contact": "13112345678,13212345679",
     *          "businessCircleId": 33,
     *          "businessCircle": "城中心核心商圈",
     *          "address": "海口市龙华区滨海大道100号",
     *          "lnglat": "100.3331,22.8888",
     *          "goodsCount": 100,
     *          "salesCount": 200,
     *          "orderCount": 300,
     *          "salesPrice": 200.12
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[GetMapping(path: '/merchant/detail/{id}')]
    public function handle(int $id): ResponseInterface
    {
        $res = $this->merchantService->detailInfo($id);
        if (!empty($res)) {
            return $this->success($res);
        }
        return $this->error('未找到对应的商家信息',ErrorCode::MODEL_NOT_FOUND);
    }
}

