<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Post;

use App\AdminRbac\Model\Origin\Post as Base;
use Hyperf\Database\Model\SoftDeletes;

class Post extends Base
{
    use SoftDeletes;

    public static function exitsByName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
