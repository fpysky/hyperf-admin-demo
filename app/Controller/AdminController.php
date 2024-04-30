<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Exception\SystemErrException;
use App\Exception\UnprocessableEntityException;
use App\Extend\Auth\AuthManager;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Admin;
use App\Request\AdminStoreRequest;
use App\Request\AdminUpdateRequest;
use App\Request\ResetPasswordRequest;
use App\Request\UpStatusRequest;
use App\Resource\AdminResource;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Stringable\Str;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\SignatureException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class AdminController extends AbstractController
{
    #[Inject]
    protected AuthManager $auth;

    #[PostMapping(path: 'admin')]
    #[Post(path: 'admin', summary: '添加管理员', tags: ['系统管理/管理员管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['name', 'mobile', 'password', 'rePassword', 'email', 'deptId', 'postId', 'status'],
        properties: [
            new Property(property: 'name', description: '用户名', type: 'string', example: ''),
            new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
            new Property(property: 'rePassword', description: '确认密码', type: 'string', example: 'admin123456'),
            new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
            new Property(property: 'deptId', description: '部门id', type: 'array', example: [1]),
            new Property(property: 'postId', description: '职位id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
            new Property(property: 'roleIds', description: '角色id数组', type: 'array', items: new Items(type: 'integer')),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员添加成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function create(AdminStoreRequest $request): ResponseInterface
    {
        $name = $request->string('name');
        $mobile = $request->string('mobile');
        $password = $request->string('password');
        $roleIds = $request->array('roleIds');
        $status = $request->integer('status');
        $email = $request->string('email');

        try {
            Db::beginTransaction();

            $admin = new Admin();
            $admin->name = $name;
            $admin->password = encryptPassword($password);
            $admin->status = $status;
            $admin->type = Admin::TYPE_NORMAL;
            $admin->mobile = $mobile;
            $admin->email = $email;
            $admin->saveOrFail();

            $admin->setRole($roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new SystemErrException("管理员添加失败:{$throwable->getMessage()}");
        }

        return $this->message('管理员添加成功');
    }

    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: 'admin')]
    #[Delete(path: 'admin', summary: '管理员删除', tags: ['系统管理/管理员管理'])]
    #[PathParameter(name: 'ids', description: '管理员id集合', required: true, schema: new Schema(type: 'string'), example: '1,2')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员删除成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function destroy(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids', []);

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能删除超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('管理员删除成功');
    }

    #[PutMapping(path: 'admin')]
    #[Put(path: 'admin', summary: '修改管理员', tags: ['系统管理/管理员管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'name', 'mobile', 'password', 'rePassword', 'email', 'deptId', 'postId', 'status', 'roleIds'],
        properties: [
            new Property(property: 'id', description: '管理员id', type: 'integer', example: 1),
            new Property(property: 'name', description: '用户名', type: 'string', example: ''),
            new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
            new Property(property: 'rePassword', description: '确认密码', type: 'string', example: 'admin123456'),
            new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
            new Property(property: 'deptIds', description: '部门id', type: 'array', example: [1]),
            new Property(property: 'postId', description: '职位id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
            new Property(property: 'roleIds', description: '角色id数组', type: 'array', items: new Items(type: 'integer')),
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
        $roleIds = (array) $request->input('roleIds');
        $status = $request->input('status');
        $email = $request->input('email');

        $admin = Admin::findFromCacheOrFail($id);

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员不能编辑');
        }

        try {
            Db::beginTransaction();

            $admin->name = $name;
            $admin->status = $status;
            $admin->mobile = $mobile;
            $admin->email = $email;
            $admin->saveOrFail();

            $admin->setRole($roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new GeneralException(ErrorCode::SERVER_ERROR, "管理员编辑失败:{$throwable->getMessage()}");
        }

        return $this->message('管理员编辑成功');
    }

    #[GetMapping(path: 'admin')]
    #[Get(path: 'admin', summary: '管理员列表', tags: ['系统管理/管理员管理'])]
    #[QueryParameter(name: 'page', description: '页码', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'keyword', description: '搜索关键词', required: false, schema: new Schema(type: 'string'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['total', 'list'],
                properties: [
                    new Property(property: 'total', description: '数据总数', type: 'integer', example: 100),
                    new Property(
                        property: 'list',
                        description: '',
                        type: 'array',
                        items: new Items(
                            required: [
                                'id', 'name', 'status', 'type',
                                'mobile', 'email', 'lastLoginIp',
                                'logo', 'deptIds', 'postId', 'lastLoginTime',
                                'roleIds', 'createdAt', 'updatedAt',
                            ],
                            properties: [
                                new Property(property: 'id', description: '管理员id', type: 'integer', example: ''),
                                new Property(property: 'name', description: '姓名', type: 'string', example: ''),
                                new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                                new Property(property: 'type', description: '类型：1.超级管理员（拥有所有权限） 2.其他', type: 'integer', example: ''),
                                new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
                                new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
                                new Property(property: 'lastLoginIp', description: '最后登陆ip', type: 'string', example: ''),
                                new Property(property: 'logo', description: '头像logo', type: 'string', example: ''),
                                new Property(property: 'deptIds', description: '部门ids', type: 'integer', example: [1]),
                                new Property(property: 'roleIds', description: '部门ids', type: 'integer', example: [1]),
                                new Property(property: 'postId', description: '职位id', type: 'integer', example: ''),
                                new Property(property: 'lastLoginTime', description: '最后登陆时间', type: 'string', example: ''),
                                new Property(property: 'createdAt', description: '创建时间', type: 'string', example: ''),
                                new Property(property: 'updatedAt', description: '更新时间', type: 'string', example: ''),
                            ]
                        )
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);
        $keyword = (string) $this->request->input('keyword');

        $builder = Admin::query()
            ->with(['adminDept', 'adminRole'])
            ->orderByDesc('id');

        if (Str::length($keyword) !== 0) {
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $paginator = $builder->paginate($pageSize);

        return $this->success([
            'list' => AdminResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }

    #[GetMapping(path: 'admin/{id:\d+}')]
    #[Get(path: 'admin/{id}', summary: '管理员详情', tags: ['系统管理/管理员管理'])]
    #[PathParameter(name: 'id', description: '管理员id', required: true, schema: new Schema(type: 'integer'), example: 1)]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: [
                    'id', 'name', 'status', 'type',
                    'mobile', 'email', 'lastLoginIp',
                    'logo', 'deptId', 'postId',
                    'lastLoginTime', 'roleIds',
                ],
                properties: [
                    new Property(property: 'id', description: '管理员id', type: 'integer', example: ''),
                    new Property(property: 'name', description: '姓名', type: 'string', example: ''),
                    new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                    new Property(property: 'type', description: '类型：1.超级管理员（拥有所有权限） 2.其他', type: 'integer', example: ''),
                    new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
                    new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
                    new Property(property: 'lastLoginIp', description: '最后登陆ip', type: 'string', example: ''),
                    new Property(property: 'logo', description: '头像logo', type: 'string', example: ''),
                    new Property(property: 'deptIds', description: '部门ids', type: 'array', example: [1]),
                    new Property(property: 'postId', description: '职位id', type: 'integer', example: ''),
                    new Property(property: 'lastLoginTime', description: '最后登陆时间', type: 'string', example: ''),
                    new Property(
                        property: 'roleIds',
                        description: '角色id数组',
                        type: 'array',
                        items: new Items(type: 'integer', example: 1)
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function detail(int $id): ResponseInterface
    {
        $admin = Admin::query()
            ->with(['adminRole', 'adminDept'])
            ->findOrFail($id);

        $data = [
            'id' => $admin->id,
            'name' => $admin->name,
            'status' => $admin->status,
            'type' => $admin->type,
            'mobile' => $admin->mobile,
            'email' => $admin->email,
            'lastLoginIp' => $admin->last_login_ip,
            'logo' => $admin->logo,
            'lastLoginTime' => $admin->last_login_time,
            'roleIds' => $admin->roleIds(),
        ];

        return $this->success($data);
    }

    #[PatchMapping(path: 'admin/resetPassword')]
    #[Patch(path: 'admin/resetPassword', summary: '重置管理员密码', tags: ['系统管理/管理员管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password'],
        properties: [
            new Property(property: 'id', description: '用户id', type: 'integer', example: 1),
            new Property(property: 'password', description: '重置密码', type: 'string', example: 'admin123456'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员密码重置成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function resetPassword(ResetPasswordRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $password = (string) $request->input('password');

        $admin = Admin::findFromCacheOrFail($id);

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员禁止重置密码');
        }

        $admin->password = encryptPassword($password);
        $admin->save();

        return $this->message('管理员密码重置成功');
    }

    /**
     * @throws SignatureException
     * @throws \RedisException
     * @throws InvalidTokenException
     * @throws TokenExpiredException
     */
    #[PatchMapping(path: 'admin/status')]
    #[Patch(path: 'admin/status', summary: '修改管理员状态', tags: ['系统管理/管理员管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['ids', 'status'],
        properties: [
            new Property(property: 'ids', description: '管理员id数组', type: 'array', items: new Items(type: 'integer')),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员启用成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function changeStatus(UpStatusRequest $request): ResponseInterface
    {
        $ids = (array) $request->input('ids');
        $status = (int) $request->input('status');

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能禁用超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        // 禁用时，强制退出
        if ($status === Admin::STATUS_DISABLED) {
            $this->auth->batchLogoutByAdmin($ids);
        }

        $action = $status === Admin::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("管理员{$action}成功");
    }
}
