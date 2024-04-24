<?php

declare(strict_types=1);

namespace App\Model\Dept\Traits;

use App\Model\Dept\Dept;
use Hyperf\Database\Model\Relations\HasMany;

trait DeptRelationship
{
    public function children(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id', 'id');
    }

    public function enabledChildren(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id', 'id')
            ->where('status', self::STATUS_ENABLE);
    }
}
