<?php

namespace App\Actions\User;

use App\Actions\AbstractAction;
use App\Services\User\UserService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class SearchListPageAction extends AbstractAction
{


    /**
     * @api {get} /user/searcher 用户列表
     * @apiVersion 1.0.0
     * @apiName 用户列表
     * @apiGroup 用户管理-用户列表
     * @apiDescription 用户列表接口 两只羊  2023/04/04
     *
     * @apiParam {int} [page] 页码，默认1开始
     * @apiParam {int} [pageSize] 页码，默认10
     * @apiParam {string} [nickname] 昵称
     * @apiParam {string} [phone] 手机号
     * @apiParam {int} [status] 状态 0-禁用，1-正常
     * @apiParam {int} [totalSpentStart] 累计消费起始金额
     * @apiParam {int} [totalSpentEnd] 累计消费结束金额
     * @apiParam {string} [createdStart] 注册开始时间
     * @apiParam {string} [createdEnd] 注册结束时间
     *
     * @apiSuccess (正确返回参数) {int} code 业务状态码
     * @apiSuccess (正确返回参数) {string} msg 返回信息说明
     * @apiSuccess (正确返回参数) {Object} data 返回数据
     * @apiSuccess (正确返回参数) {array} data.list 用户列表
     * @apiSuccess (正确返回参数) {int} data.list.id 用户ID
     * @apiSuccess (正确返回参数) {string} data.list.headimgurl 头像
     * @apiSuccess (正确返回参数) {string} data.list.nickname 昵称
     * @apiSuccess (正确返回参数) {string} data.list.phone 手机
     * @apiSuccess (正确返回参数) {int} data.list.totalSpent 消费总金额
     * @apiSuccess (正确返回参数) {int} data.list.totalOrders 消费总订单
     * @apiSuccess (正确返回参数) {int} data.list.status 状态：0-禁用，1-正常
     * @apiSuccess (正确返回参数) {string} data.list.createdTime 注册时间
     *
     * @apiSuccessExample  {json} 正确返回值
     * {
     *   "code": 200000,
     *   "msg": "",
     *   "data": {
     *       "total":10,
     *       "list": [
     *           {
     *              "id": 1,
     *              "headimgurl": "/sdfs/sfsdf/sfs.jpg",
     *              "nickname": "张三",
     *              "phone": "18888888888",
     *              "totalSpent": 1222,
     *              "status": 1,
     *              "totalOrders": 1,
     *              "createdTime": "2021-08-26 07:28"
     *          }
     *       ]
     *   }
     * }
     * @apiError {int} code 请求状态码，非200
     * @apiError {string} message 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":404000,"message":"数据异常,请重试","data":{}}
     */
    #[RequestMapping(path: '/user/searcher', methods: 'GET')]
    public function handle(): ResponseInterface
    {

        $page = (int)$this->request->input('page', 1);
        $pageSize = (int)$this->request->input('pageSize', 10);
        $nickname = $this->request->input('nickname', '');
        $phone = $this->request->input('phone', '');
        $status = $this->request->input('status', '');
        $totalSpentStart = $this->request->input('totalSpentStart', '');
        $totalSpentEnd = $this->request->input('totalSpentEnd', '');
        $createdStart = $this->request->input('createdStart', '');
        $createdEnd = $this->request->input('createdEnd', '');

        $data = make(UserService::class)->searchUserPageList($page, $pageSize, $nickname, $phone, $status, $totalSpentStart, $totalSpentEnd, $createdStart, $createdEnd);

        if ($data) {
            foreach ($data as &$value) {
                $value['totalSpent'] = $value['totalSpent'] ?: 0;
                $value['totalOrders'] = $value['totalOrders'] ?: 0;
            }
        }

        return $this->success($data,'成功！');
    }


}
