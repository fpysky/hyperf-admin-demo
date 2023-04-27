<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\AdminStoreRequest;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
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
            $admin->password = Admin::encryptPassword($password);
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
            throw new GeneralException(ErrorCode::SERVER_ERROR, "管理员添加失败:{$throwable->getMessage()}");
        }

        return $this->message('管理员添加成功');
    }
}
