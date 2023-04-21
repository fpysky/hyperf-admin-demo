<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Enums\PostEnums;
use App\AdminRbac\Model\Post;
use App\AdminRbac\Request\PostStoreRequest;
use App\AdminRbac\Request\PostUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'post')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class PostController extends AbstractAction
{
    /**
     * 岗位列表
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     */
    #[GetMapping(path: '/system/backend/backendAdminPost/page')]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);

        $paginate = Post::query()
            ->select([
                'id', 'name', 'status', 'order as sort',
                'mark as remark', 'created_at as createTime',
            ])
            ->orderBy('order')
            ->paginate($pageSize);

        return $this->success($paginate);
    }

    /**
     * 岗位添加.
     * @param PostStoreRequest $request
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[PostMapping(path: '/system/backend/backendAdminPost')]
    public function store(PostStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');

        if (Post::exitsByName($name)) {
            throw new UnprocessableEntityException('岗位已存在');
        }

        $data = [
            'name' => (string) $request->input('name'),
            'status' => (int) $request->input('status'),
            'order' => (int) $request->input('sort'),
            'mark' => (string) $request->input('remark'),
        ];

        Post::query()->create($data);

        return $this->message('岗位添加成功');
    }

    /**
     * 岗位编辑.
     * @param PostUpdateRequest $request
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[PutMapping(path: '/system/backend/backendAdminPost')]
    public function update(PostUpdateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $id = (int) $request->input('id');

        if (Post::exitsByName($name, $id)) {
            throw new UnprocessableEntityException('岗位已存在');
        }

        $data = [
            'name' => (string) $request->input('name'),
            'status' => (int) $request->input('status'),
            'order' => (int) $request->input('sort'),
            'mark' => (string) $request->input('remark'),
        ];

        Post::query()
            ->where('id', $id)
            ->update($data);

        return $this->message('岗位编辑成功');
    }

    /**
     * 岗位删除.
     * @param string $ids
     * @return ResponseInterface
     * @throws \Exception
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[DeleteMapping(path: '/system/backend/backendAdminPost/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $ids = array_filter($ids);

        Post::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('岗位删除成功');
    }

    /**
     * 岗位状态改变
     * User: ZhouGongCe
     * Time: 2021/8/13 16:13.
     */
    #[PutMapping(path: '/system/backend/backendAdminPost/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = $this->request->input('ids');
        $status = $this->request->input('status');

        if ($status == PostEnums::USE) {
            $status = PostEnums::USE;
            $msg = '岗位启用成功';
        } else {
            $status = PostEnums::DISABLE;
            $msg = '岗位禁用成功';
        }

        Post::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        return $this->message($msg);
    }

    /**
     * 下拉组件所有岗位列表.
     * @return ResponseInterface
     * @author weixiaohui
     * @email  xh_wei@juling.vip
     * @date   2021/12/30
     */
    #[GetMapping(path: '/system/backend/backendAdminPost/postCombobox')]
    public function all(): ResponseInterface
    {
        $list = Post::query()
            ->select(['id', 'name as label'])
            ->where('status', 1)
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success($list ? $list->toArray() : []);
    }

    /**
     * 岗位详情.
     * @param int $id
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/3
     * @modifier fengpengyuan 2023/4/3
     */
    #[GetMapping(path: '/system/backend/backendAdminPost/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        try {
            /** @var Post $post */
            $post = Post::query()
                ->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('岗位不存在');
        }

        $data = [
            'id' => $post->id,
            'name' => $post->name,
            'remark' => $post->mark,
            'sort' => $post->order,
            'status' => $post->status,
        ];

        return $this->success($data);
    }
}
