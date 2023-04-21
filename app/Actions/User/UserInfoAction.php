<?php

namespace App\Actions\User;


use App\Actions\AbstractAction;
use App\Services\User\UserService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class UserInfoAction extends AbstractAction
{

    /**
     * @api {get} /user/info 用户详情
     * @apiVersion 1.0.0
     * @apiName 用户详情
     * @apiGroup 用户管理-用户列表
     * @apiDescription 用户详情接口 两只羊  2023/04/04
     *
     * @apiParam {int} [id] 用户id
     *
     * @apiSuccess (正确返回参数) {int} code 业务状态码
     * @apiSuccess (正确返回参数) {string} msg 返回信息说明
     * @apiSuccess (正确返回参数) {Object} data 用户信息
     * @apiSuccess (正确返回参数) {int} data.id 用户ID
     * @apiSuccess (正确返回参数) {string} data.headimgurl 头像
     * @apiSuccess (正确返回参数) {string} data.nickname 昵称
     * @apiSuccess (正确返回参数) {string} data.phone 手机
     * @apiSuccess (正确返回参数) {int} data.totalSpent 消费总金额
     * @apiSuccess (正确返回参数) {int} data.totalOrders 支付总订单
     * @apiSuccess (正确返回参数) {int} data.status 状态：0-禁用，1-正常
     * @apiSuccess (正确返回参数) {int} data.points 用户积分
     * @apiSuccess (正确返回参数) {int} data.couponTotal 优惠券总数
     * @apiSuccess (正确返回参数) {int} data.useCouponTotal 使用优惠券总数
     * @apiSuccess (正确返回参数) {int} data.cancelCouponTotal 作废优惠券总数
     * @apiSuccess (正确返回参数) {int} data.inviteFriend 邀请好友总数
     * @apiSuccess (正确返回参数) {string} data.createdTime 注册时间
     *
     * @apiSuccessExample  {json} 正确返回值
     * {
     *   "code": 200000,
     *   "msg": "",
     *   "data": {
     *      "id": 1,
     *      "headimgurl": "/sdfs/sfsdf/sfs.jpg",
     *      "nickname": "张三",
     *      "phone": "18888888888",
     *      "totalSpent": 1222,
     *      "totalOrders": 1222,
     *      "status": 1,
     *      "points": 1,
     *      "couponTotal": 1,
     *      "useCouponTotal": 1,
     *      "cancelCouponTotal": 1,
     *      "inviteFriend": 1,
     *      "createdTime": "2021-08-26 07:28"
     *   }
     * }
     * @apiError {string} code 请求状态码，非200
     * @apiError {string} message 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":"404000","message":"数据异常,请重试","data":[]}
     */
    #[RequestMapping(path: '/user/info', methods: 'GET')]
    public function handle(): ResponseInterface
    {
        $id = (int)$this->request->input('id', 0);
        $userService = make(UserService::class);
        $data = $userService->getUserInfoById($id);
        $list = [];
        $list['id'] = isset($data['id']) ? $data['id'] : 0;
        $list['nickname'] = isset($data['nickname']) ? $data['nickname'] : '';
        $list['headimgurl'] = isset($data['headimgurl']) ? $data['headimgurl'] : '';
        $list['phone'] = isset($data['phone']) ? $data['phone'] : '';
        $list['totalSpent'] = isset($data['total_spent']) ? $data['total_spent'] : 0;
        $list['status'] = isset($data['status']) ? $data['status'] : 1;
        $list['totalOrders'] = isset($data['total_orders']) ? $data['total_orders'] : 0;
        $list['createdTime'] = isset($data['created_at']) ? $data['created_at'] : '';
        $list['points'] = 0;//TODO
        $list['couponTotal'] = 0;//TODO
        $list['useCouponTotal'] = 0;//TODO
        $list['cancelCouponTotal'] = 0;//TODO
        $list['inviteFriend'] = 0;//TODO

        return $this->success($list, '成功');
    }

}