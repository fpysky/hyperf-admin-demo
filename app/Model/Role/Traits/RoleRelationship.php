<?php

declare(strict_types=1);

namespace App\Model\Role\Traits;

use App\Model\Role\RoleRule;
use Hyperf\Database\Model\Relations\HasMany;

trait RoleRelationship
{
    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'role_id', 'id');
    }
}
