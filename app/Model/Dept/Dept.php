<?php

declare(strict_types=1);

namespace App\Model\Dept;

use App\Exception\RecordNotFoundException;
use App\Model\Dept\Traits\DeptRelationship;
use App\Model\Origin\Dept as Base;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property Collection $children
 * @property Collection $enabledChildren
 */
class Dept extends Base
{
    use SoftDeletes;
    use DeptRelationship;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;
    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;

    public static function existName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('部门不存在');
        }

        return $model;
    }
}
