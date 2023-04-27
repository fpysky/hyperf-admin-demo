<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\AdminUpdateRequest;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Exception\UnprocessableEntityException;
use App\Extend\Log\Log;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
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

        $admin = Admin::query()->findOrFail($id);

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员不能编辑');
        }

        try {
            Db::beginTransaction();

            $storePassword = password_hash($password, PASSWORD_DEFAULT);

            $admin->name = $name;
            $admin->password = $storePassword;
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
