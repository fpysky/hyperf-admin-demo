<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\AdminStoreRequest;
use App\Exception\SystemErrException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class CreateAction extends AbstractAction
{
    /**
     * @throws \Exception
     */
    #[PostMapping(path: '')]
    #[Post(path: '/admin',summary: '添加管理员',tags: ['后台管理/系统管理/管理员'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password'],
        properties: [
            new Property(property: 'name', description: '用户名', type: 'string', example: ''),
            new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
            new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
            new Property(property: 'deptId', description: '部门id', type: 'integer', example: 1),
            new Property(property: 'postId', description: '职位id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态：1启用 2 停用', type: 'integer', example: 1),
        ]
    ))]
    public function handle(AdminStoreRequest $request): ResponseInterface
    {
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $roleIds = (array) $request->input('roleIds');
        $status = $request->input('status');
        $email = $request->input('email');
        $deptId = $request->input('deptId');
        $postId = $request->input('postId');

        try {
            Db::beginTransaction();

            $admin = new Admin();

            $admin->name = $name;
            $admin->password = encryptPassword($password);
            $admin->status = $status;
            $admin->type = Admin::TYPE_NORMAL;
            $admin->mobile = $mobile;
            $admin->email = $email;
            $admin->dept_id = $deptId;
            $admin->post_id = $postId;

            $admin->saveOrFail();
            $admin->setRole($roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new SystemErrException("管理员添加失败:{$throwable->getMessage()}");
        }

        return $this->message('管理员添加成功');
    }
}
