<?php

declare(strict_types=1);

namespace App\Dao\Area;

use App\Exception\GeneralException;
use App\Model\Area\Area;
use App\Resource\AreaInfo;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Resource\Json\AnonymousResourceCollection;
use Hyperf\Utils\Str;

class AreaDao
{
    #[Inject]
    protected Area $areaModel;

    /**
     * @param int $pid
     * @param string $name
     * @param int $sortOrder
     * @author YuYun 2023/4/7
     * @modifier YuYun 2023/4/7
     */
    public function create(int $pid, string $name, int $sortOrder): void
    {
        $this->areaModel->create([
            'pid' => $pid,
            'name' => $name,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * @param int $id
     * @param int $pid
     * @param string $name
     * @param int $sortOrder
     * @author YuYun 2023/4/7
     * @modifier YuYun 2023/4/7
     */
    public function update(int $id, int $pid, string $name, int $sortOrder): void
    {
        $params = [];
        if ($pid) {
            $params['pid'] = $pid;
        }
        if ($name) {
            $params['name'] = $name;
        }
        if ($sortOrder) {
            $params['sort_order'] = $sortOrder;
        }
        if (empty($params)) {
            throw new GeneralException('更新参数不能为空');
        }

        $this->areaModel->findByIdOrFail($id)->update($params);
    }

    /**
     * @param int $id
     * @author YuYun 2023/4/7
     * @modifier YuYun 2023/4/7
     */
    public function del(int $id): void
    {
        $this->areaModel->where('id', '=', $id)->delete();
    }

    public function pageList(string $name, int $page, int $pageSize, array $orderBy = null): AnonymousResourceCollection
    {
        $where = [];
        if (Str::length($name) !== 0) {
            $where[] = ['name', 'like', "%$name%"];
        }
        $builder = $this->areaModel->getBuilderBySimpleQueries($where);

        if (is_null($orderBy)) {
            $orderBy = ['id' => 'desc'];
        }
        foreach ($orderBy as $key => $value) {
            $builder->orderBy($key, $value);
        }

        $list = $this->areaModel->pageList($page, $pageSize, $where, $orderBy);
        return AreaInfo::collection($list);
    }

    public function detail(int $id): array
    {
        $info = $this->areaModel->findByIdOrFail($id);
        return (new AreaInfo($info))->toArray();
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
        return $this->areaModel->total($where);
    }

    /**
     * @param int $fid
     * @param string $name
     * @param int $exceptId
     * @return bool
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function existsByName(int $fid, string $name, int $exceptId = 0): bool
    {
        $builder = $this->areaModel->query()
            ->where([
                'fid' => $fid,
                'name' => $name,
            ]);

        if ($exceptId !== 0) {
            $builder->where('id', $exceptId);
        }

        return $builder->exists();
    }
}
