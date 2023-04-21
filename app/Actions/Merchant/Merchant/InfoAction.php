<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class InfoAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {get} /merchant/merchant/{id} 查看商家
     * @apiDescription 查看商家  yulu 2023/4/4
     * @apiName 查看商家
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
     * @apiSuccess {string} data.categoryId 商家分类ID 多个用,分开
     * @apiSuccess {string} data.coverUrl 商家封面地址
     * @apiSuccess {string} data.recommendation 推荐语
     * @apiSuccess {string} data.videoUrl 宣传视频地址
     * @apiSuccess {string} data.imageUrls  宣传图片地址 多个,分割
     * @apiSuccess {integer} data.businessHourType  0/1/2
     * @apiSuccess {string} data.businessHours 营业时间 json格式
     * @apiSuccess {string} data.contact 联系方式 多个用,分割
     * @apiSuccess {integer} data.areaId 所在地区ID
     * @apiSuccess {integer} data.businessCircleId 所属商圈ID
     * @apiSuccess {string} data.address 详细地址
     * @apiSuccess {string} data.lnglat 定位(格式: lng,lat)
     * @apiSuccess {string} data.businessLicenseUrl 营业执照图片地址
     * @apiSuccess {string} data.legalPersonIdcardFrontUrl 法人身份证正面照片地址
     * @apiSuccess {string} data.legalPersonIdcardBackUrl 法人身份证反面照片地址
     * @apiSuccess {string} data.otherQalificationNames 其它资质名称,多个用,分割
     * @apiSuccess {string} data.otherQalificationUrls 其它资质图片地址列表,多个用,分割
     * @apiSuccessExample {json}
     *  {
     *    "code": "200000",
     *    "msg": "",
     *    "data": {
     *          "id": 100,
     *          "phone": "13012345678",
     *          "merchantName": "龙华小茗商店",
     *          "categoryId": "12,13",
     *          "coverUrl": "http://www.cdn.img/afaf1.jpg",
     *          "recommendation": "最好的小吃店",
     *          "videoUrl": "http://www.cdn.img/afaf1.mp4",
     *          "imageUrls": "http://www.cdn.img/afaf1.jpg,http://www.cdn.img/afaf2.jpg",
     *          "businessHourType:0,
     *          "businessHours "[]",
     *          "contact": "13112345678,13212345679",
     *          "areaId": 22,
     *          "businessCircleId": 33,
     *          "address": "海口市龙华区滨海大道100号",
     *          "lnglat": "100.3331,22.8888",
     *          "businessLicenseUrl": "http://www.cdn.img/afaf1.jpg",
     *          "legalPersonIdcardFrontUrl": "http://www.cdn.img/afaf1.jpg",
     *          "legalPersonIdcardBackUrl": "http://www.cdn.img/afaf1.jpg",
     *          "otherQalificationNames":"卫生许可证,营业许可证",
     *           "otherQalificationUrls": "http://www.cdn.img/afaf1.jpg,http://www.cdn.img/afaf2.jpg",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[GetMapping(path: '/merchant/merchant/{id}')]
    public function handle(int $id): ResponseInterface
    {
        // return $this->success(['merchant'=>'merchant']);
        $res = $this->merchantService->getInfo($id);
        if (!empty($res)) {
            //商家categoryId
            $categoryInfo = $this->merchantService->getMerchantCateInfo($id);
            $cateIdArr = $categoryInfo['id'] ?? [];
            $res['categoryId']   = implode(',',$cateIdArr);
            return $this->success($res);
        }
        return $this->error('未找到对应的商家信息',ErrorCode::MODEL_NOT_FOUND);
    }
}
