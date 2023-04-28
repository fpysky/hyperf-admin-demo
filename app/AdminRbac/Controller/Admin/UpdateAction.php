<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\AdminUpdateRequest;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class UpdateAction extends AbstractAction
{
    /**
     * @throws \Exception
     */
    #[PutMapping(path: '')]
    #[Put(path: '/admin', summary: '修改管理员', tags: ['后台管理/系统管理/管理员'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'name', 'mobile', 'password', 'email', 'deptId', 'postId', 'status'],
        properties: [
            new Property(property: 'id', description: '管理员id', type: 'string', example: 1),
            new Property(property: 'name', description: '用户名', type: 'string', example: ''),
            new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
            new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
            new Property(property: 'deptId', description: '部门id', type: 'integer', example: 1),
            new Property(property: 'postId', description: '职位id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员编辑成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function update(AdminUpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $roleIds = (array) $request->input('roleIds');
        $status = $request->input('status');
        $email = $request->input('email');
        $deptId = $request->input('deptId');
        $postId = $request->input('postId');

        $admin = Admin::findFromCacheOrFail($id);

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员不能编辑');
        }

        try {
            Db::beginTransaction();

            $admin->name = $name;
            $admin->password = encryptPassword($password);
            $admin->status = $status;
            $admin->mobile = $mobile;
            $admin->email = $email;
            $admin->dept_id = $deptId;
            $admin->post_id = $postId;
            $admin->saveOrFail();

            $admin->setRole($roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new GeneralException(ErrorCode::SERVER_ERROR, "管理员编辑失败:{$throwable->getMessage()}");
        }

        return $this->message('管理员编辑成功');
    }
}
