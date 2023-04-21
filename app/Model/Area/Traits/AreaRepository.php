<?php

declare(strict_types=1);

namespace App\Model\Area\Traits;

use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Model\Area\Area;
use Hyperf\Contract\PaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\ModelNotFoundException;

trait AreaRepository
{
    /**
     * @param array $where
     * @param array $orderBy
     * @return Builder
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function baseSimpleBuilder(array $where, array $orderBy = []): Builder
    {
        $builder = self::query();
        foreach ($where as $key => $value) {
            if ($value[1] == 'between') {
                $builder->whereBetween($value[0], $value[2]);
                unset($where[$key]);
            }
            if ($value[1] == 'in') {
                $builder->whereIn($value[0], $value[2]);
                unset($where[$key]);
            }
            if ($value[1] == 'not in') {
                $builder->whereNotIn($value[0], $value[2]);
                unset($where[$key]);
            }
        }
        $builder->where($where);
        foreach ($orderBy as $key => $value) {
            $builder->orderBy($key, $value);
        }
        return $builder;
    }

    /**
     * @param int $id
     * @param array $selects
     * @return Area
     * @author YuYun 2023/4/7
     * @modifier YuYun 2023/4/7
     */
    public function findByIdOrFail(int $id, array $selects = ['*']): Area
    {
        try {
            /** @var Area $area */
            return Area::query()
                ->select($selects)
                ->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new GeneralException(
                ErrorCode::MODEL_NOT_FOUND,
                ErrorCode::getMessage(ErrorCode::MODEL_NOT_FOUND)
            );
        }
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param array $where
     * @param array $orderBy
     * @return PaginatorInterface
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function pageList(int $page, int $perPage, array $where, array $orderBy)
    {
        return $this->baseSimpleBuilder($where, $orderBy)->simplePaginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param array $where
     * @return int
     * @author YuYun 2023/4/8
     * @modifier YuYun 2023/4/8
     */
    public function total(array $where): int
    {
        return $this->baseSimpleBuilder($where)->count();
    }
}
