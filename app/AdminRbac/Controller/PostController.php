<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Post\Post;
use App\AdminRbac\Request\PostStoreRequest;
use App\AdminRbac\Request\PostUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
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
    #[GetMapping(path: 'system/backend/backendAdminPost/page')]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);

        $paginator = Post::query()
            ->select([
                'id', 'name', 'status', 'order as sort',
                'mark as remark', 'created_at as createTime',
            ])
            ->orderBy('order')
            ->paginate($pageSize);

        return $this->success($paginator);
    }

    #[PostMapping(path: 'system/backend/backendAdminPost')]
    public function store(PostStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');

        if (Post::existName($name)) {
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

    #[PutMapping(path: 'system/backend/backendAdminPost')]
    public function update(PostUpdateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $id = (int) $request->input('id');

        if (Post::existName($name, $id)) {
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
     * @throws \Exception
     */
    #[DeleteMapping(path: 'system/backend/backendAdminPost/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $ids = array_filter($ids);

        Post::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('岗位删除成功');
    }

    #[PutMapping(path: 'system/backend/backendAdminPost/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids');
        $status = (int) $this->request->input('status');

        Post::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        if ($status == Post::STATUS_ENABLE) {
            $msg = '岗位启用成功';
        } else {
            $msg = '岗位禁用成功';
        }

        return $this->message($msg);
    }

    #[GetMapping(path: 'system/backend/backendAdminPost/postCombobox')]
    public function postCombobox(): ResponseInterface
    {
        $list = Post::query()
            ->select(['id', 'name as label'])
            ->where('status', 1)
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success($list);
    }

    #[GetMapping(path: 'system/backend/backendAdminPost/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        $post = Post::query()->findOrFail($id);

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
