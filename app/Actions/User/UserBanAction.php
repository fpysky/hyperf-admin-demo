<?php

namespace App\Actions\User;


use App\Actions\AbstractAction;
use App\Services\User\UserService;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class UserBanAction extends AbstractAction
{

    /**
     * @api {post} /user/ban 封禁用户
     * @apiVersion 1.0.0
     * @apiName 封禁用户
     * @apiGroup 用户管理-用户列表
     * @apiDescription 封禁用户接口 两只羊  2023/04/04
     *
     * @apiParam {int} [id] 用户id
     *
     * @apiSuccess (正确返回参数) {int} code 业务状态码
     * @apiSuccess (正确返回参数) {string} msg 返回信息说明
     * @apiSuccess (正确返回参数) {Object} data 用户信息
     *
     * @apiSuccessExample  {json} 正确返回值
     * {
     *   "code": 200000,
     *   "msg": "成功",
     *   "data": {
     *   }
     * }
     * @apiError {int} code 请求状态码，非200
     * @apiError {string} msg 请求状态码描述
     * @apiError {object} data 请求内容
     * @apiErrorExample {json} Error-Response:
     *{"code":404000,"msg":"数据异常,请重试","data":{}}
     */
    #[RequestMapping(path: '/user/ban', methods: 'POST')]
    public function handle(): ResponseInterface
    {
        $id = (int)$this->request->input('id', 0);
        if (!$id) {
            return $this->error('id不能为空！');
        }
        try {
            if (make(UserService::class)->ban($id)) {
                return $this->success('成功');
            } else {
                return $this->error('失败');
            }
        } catch (ModelNotFoundException $exception) {
            return $this->error('数据库操作失败:' . $exception->getMessage());
        }


    }

}