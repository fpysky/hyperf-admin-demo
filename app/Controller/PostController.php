<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Post;
use App\Request\PostStoreRequest;
use App\Request\PostUpdateRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'post')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class PostController extends AbstractController
{
    #[GetMapping(path: 'system/backend/backendAdminPost/page')]
    public function index(): ResponseInterface
    {
        $paginator = Post::query()
            ->select([
                'id', 'name', 'status', 'order as sort',
                'mark as remark', 'created_at as createTime',
            ])
            ->orderBy('order')
            ->paginate();

        return $this->success($paginator);
    }

    #[PostMapping(path: 'system/backend/backendAdminPost')]
    public function store(PostStoreRequest $request): ResponseInterface
    {
        $name = $request->string('name');

        if (Post::existName($name)) {
            throw new UnprocessableEntityException('岗位已存在');
        }

        $data = [
            'name' => $name,
            'status' => $request->integer('status'),
            'order' => $request->integer('sort'),
            'mark' => $request->string('remark'),
        ];

        Post::query()->create($data);

        return $this->message('岗位添加成功');
    }

    #[PutMapping(path: 'system/backend/backendAdminPost')]
    public function update(PostUpdateRequest $request): ResponseInterface
    {
        $name = $request->string('name');
        $id = $request->integer('id');

        if (Post::existName($name, $id)) {
            throw new UnprocessableEntityException('岗位已存在');
        }

        $data = [
            'name' => $name,
            'status' => $request->integer('status'),
            'order' => $request->integer('sort'),
            'mark' => $request->string('remark'),
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
        $ids = $this->request->array('ids');
        $status = $this->request->integer('status');

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
            ->where('status', Post::STATUS_ENABLE)
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
            'remark' => $post->remark,
            'sort' => $post->sort,
            'status' => $post->status,
        ];

        return $this->success($data);
    }
}
