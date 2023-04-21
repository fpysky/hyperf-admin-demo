<?php

declare(strict_types=1);

namespace App\Actions\Merchant\Merchant;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Middleware\RuleMiddleware;
use App\Middleware\TokenMiddleware;
use App\Request\Merchant\Merchant\CreateRequest;
use App\Services\Merchant\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
// #[Middlewares([TokenMiddleware::class, RuleMiddleware::class])]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected MerchantService $merchantService;

    /**
     * @api {post} /merchant/merchant  新增商家
     * @apiDescription 新增商家  yulu 2023/4/4
     * @apiName 新增商家
     * @apiGroup 商家管理-商家列表
     * @apiVersion 1.0.0
     * @apiUse headers
     *
     * @apiParam {string} phone 商家账户 11位手机号
     * @apiParam {string} merchantName 商家名称
     * @apiParam {string} categoryId 商家分类ID,多个用,分开  例如 11,12
     * @apiParam {string} coverUrl 商家封面地址
     * @apiParam {string} [recommendation] 推荐语
     * @apiParam {string} [videoUrl] 宣传视频地址
     * @apiParam {string} [imageUrls]  宣传图片地址 多个,分割
     * @apiParam {integer} businessHourType    0/1/2  分别表示全天，每天，每周
     * @apiParam {string} businessHours 营业时间 json格式  示例如下:  全天:  []
     * 每天：[{ start: '', end: '', dayType: 0 }]
     * 每周：[{ week: [], times: [{ start: '', end: '', dayType: 0 }] }]
     * @apiParam {string} contact 联系方式 多个用,分割
     * @apiParam {integer} areaId 所在地区ID
     * @apiParam {integer} [businessCircleId] 所属商圈ID
     * @apiParam {string} address 详细地址
     * @apiParam {string} lnglat 定位(格式: lng,lat)
     * @apiParam {string} businessLicenseUrl 营业执照图片地址
     * @apiParam {string} [legalPersonIdcardFrontUrl] 法人身份证正面照片地址
     * @apiParam {string} [legalPersonIdcardBackUrl] 法人身份证反面照片地址
     * @apiParam {string} [otherQalificationNames] 其它资质名称,多个用,分割
     * @apiParam {string} [otherQalificationUrls] 其它资质图片地址列表,多个用,分割
     *
     * @apiSuccess {string} code 返回状态码
     * @apiSuccess {string} msg 错误提示语
     * @apiSuccess {array} data 响应内容
     * @apiSuccess {integer} data.id 商户id
     * @apiSuccess {string} data.phone 商家账户 11位手机号
     * @apiSuccess {string} data.merchantName 商家名称
     * @apiSuccess {string} data.categoryId 商家分类ID,多个用,分开
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
     *          "businessHours: "[]",
     *          "contact": "13112345678,13212345679",
     *          "areaId": 22,
     *          "businessCircleId": 33,
     *          "address": "海口市龙华区滨海大道100号",
     *          "lnglat": "100.3331,22.8888",
     *          "businessLicenseUrl": "http://www.cdn.img/afaf1.jpg",
     *          "legalPersonIdcardFrontUrl": "http://www.cdn.img/afaf1.jpg",
     *          "legalPersonIdcardBackUrl": "http://www.cdn.img/afaf1.jpg",
     *          "otherQalificationNames":"卫生许可证,营业许可证",
     *          "otherQalificationUrls": "http://www.cdn.img/afaf1.jpg,http://www.cdn.img/afaf2.jpg",
     *    }
     *}
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","msg":"数据异常,请重试","data":[]}
     */
    #[PostMapping(path: '/merchant/merchant')]
    public function handle(CreateRequest $request): ResponseInterface
    {
        // 该商家名称已存在   商家名字创建不能重复
        $params = $request->all();
        // 检查是否有重名
        if ($this->merchantService->hasSameMerchantName($params['merchantName'])) {
            return $this->error('该商家名称已存在',ErrorCode::UNPROCESSABLE_ENTITY);
        }
        $record = [];
        $record['phone'] = $params['phone'] ?? '';
        $record['merchant_name'] = $params['merchantName'] ?? '';
        //$record['category_id'] =  $params['categoryId'];
        //写入商家分类
        $record['cover_url'] = $params['coverUrl'] ?? '';
        $record['recommendation'] = $params['recommendation'] ?? '';
        $record['video_url'] = $params['videoUrl'] ?? '';
        $record['image_urls'] = $params['imageUrls'] ?? '';
        $record['business_hour_type'] = (int) $params['businessHourType'] ?? 0;
        $record['business_hours'] = $params['businessHours'] ?? '[]';
        $record['contact'] = $params['contact'] ?? '';
        $record['area_id'] = (int) $params['areaId'] ?? 0;
        $record['business_circle_id'] = 0;
        if (isset($params['businessCircleId'])) {
            $record['business_circle_id'] = (int) $params['businessCircleId'] ?? 0;
        }
        $record['address'] = $params['address'] ?? '';
        $record['lnglat'] = $params['lnglat'] ?? '';
        $lngLatArr = explode(',', $record['lnglat']);
        $record['lng'] = (float) $lngLatArr[0];
        $record['lat'] = (float) $lngLatArr[1];
        $record['business_license_url'] = $params['businessLicenseUrl'] ?? '';
        $record['legal_person_idcard_front_url'] = $params['legalPersonIdcardFrontUrl'] ?? '';
        $record['legal_person_idcard_back_url'] = $params['legalPersonIdcardBackUrl'] ?? '';
        $record['other_qualification_names'] = $params['otherQalificationNames'] ?? '';
        $record['other_qualification_urls'] = $params['otherQalificationUrls'] ?? '';

        $categoryId = $params['categoryId'];
        $id = $this->merchantService->create($categoryId,$record);
        unset($params['lng'], $params['lat']);
        //可能为空的字段处理
        //调用info信息返回，用于编辑的
        //设置categoryId
        $merchantInfo = $this->merchantService->getInfo($id);
        $merchantInfo['categoryId']  = $categoryId;
        return $this->success($merchantInfo);
    }
}
