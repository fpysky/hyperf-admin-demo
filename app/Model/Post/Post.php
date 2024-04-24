<?php

declare(strict_types=1);

namespace App\Model\Post;

use App\Model\Origin\Post as Base;
use Hyperf\Database\Model\SoftDeletes;

class Post extends Base
{
    use SoftDeletes;

    /** 状态：启用 */
    const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    const STATUS_DISABLED = 0;

    public static function existName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
