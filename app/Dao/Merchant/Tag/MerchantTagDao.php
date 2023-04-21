<?php

declare(strict_types=1);

namespace App\Dao\Merchant\Tag;

use App\Exception\GeneralException;
use App\Model\Merchant\Tag\MerchantTag;
use App\Resource\MerchantTagInfo;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Resource\Json\AnonymousResourceCollection;
use Hyperf\Utils\Str;

class MerchantTagDao
{
    #[Inject]
    protected MerchantTag $merchantTagModel;

    /**
     * @param string $name
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function create(string $name): void
    {
        $this->merchantTagModel->create([
            'name' => $name,
        ]);
    }

    /**
     * @param int $id
     * @param string $name
     * @param int $sort
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function update(int $id, string $name, int $sort): void
    {
        $params = [];
        if ($name) {
            $params['name'] = $name;
        }
        if ($sort) {
            $params['sort'] = $sort;
        }
        if (empty($params)) {
            throw new GeneralException('更新参数不能为空');
        }

        $this->merchantTagModel->findByIdOrFail($id)->update($params);
    }

    /**
     * @param int $id
     * @author YuYun 2023/4/7
     * @modifier YuYun 2023/4/7
     */
    public function del(int $id): void
    {
        $this->merchantTagModel->where('id', '=', $id)->delete();
    }

    /**
     * @param string $name
     * @param int $page
     * @param int $pageSize
     * @param null|array $orderBy
     * @return AnonymousResourceCollection
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function pageList(string $name, int $page, int $pageSize, array $orderBy = null): AnonymousResourceCollection
    {
        $where = [];
        if (Str::length($name) !== 0) {
            $where[] = ['name', 'like', "%$name%"];
        }
        $builder = $this->merchantTagModel->getBuilderBySimpleQueries($where);

        if (is_null($orderBy)) {
            $orderBy = ['id' => 'desc'];
        }
        foreach ($orderBy as $key => $value) {
            $builder->orderBy($key, $value);
        }

        $list = $this->merchantTagModel->pageList($page, $pageSize, $where, $orderBy);
        return MerchantTagInfo::collection($list);
    }

    /**
     * @param string $name
     * @param null|array $orderBy
     * @return AnonymousResourceCollection
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function list(string $name, array $orderBy = null): AnonymousResourceCollection
    {
        $where = [];
        if (Str::length($name) !== 0) {
            $where[] = ['name', 'like', "%$name%"];
        }

        $builder = $this->merchantTagModel->getBuilderBySimpleQueries($where);

        if (is_null($orderBy)) {
            $orderBy = ['id' => 'desc'];
        }

        foreach ($orderBy as $key => $value) {
            $builder->orderBy($key, $value);
        }

        $list = $this->merchantTagModel->list($where, $orderBy);
        return MerchantTagInfo::collection($list);
    }

    public function detail(int $id): array
    {
        $info = $this->merchantTagModel->findByIdOrFail($id);
        return (new MerchantTagInfo($info))->toArray();
    }

    /**
     * @param string $name
     * @return int
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function total(string $name): int
    {
        $where = [];
        if (Str::length($name) !== 0) {
            $where[] = ['name', 'like', "%$name%"];
        }
        return $this->merchantTagModel->total($where);
    }

    /**
     * @param string $name
     * @param int $exceptId
     * @return bool
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function existsByName(string $name, int $exceptId = 0): bool
    {
        $builder = $this->merchantTagModel->query()
            ->where('name', $name);

        if ($exceptId !== 0) {
            $builder->where('id', $exceptId);
        }

        return $builder->exists();
    }

    public function updateStatus(int $id, int $status): void
    {
        $this->merchantTagModel->findByIdOrFail($id)->update(['status' => $status]);
    }
}
