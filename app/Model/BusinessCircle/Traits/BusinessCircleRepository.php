<?php

declare(strict_types=1);

namespace App\Model\BusinessCircle\Traits;

trait BusinessCircleRepository
{
    public static function existsByName(string $name, int $exceptId = 0): bool
    {
        $builder = self::query()
            ->where('name', $name);

        if ($exceptId !== 0) {
            $builder->where('id', $exceptId);
        }

        return $builder->exists();
    }
}
