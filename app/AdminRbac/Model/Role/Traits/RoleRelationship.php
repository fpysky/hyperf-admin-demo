<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Role\Traits;

use App\AdminRbac\Model\Role\RoleRule;
use Hyperf\Database\Model\Relations\HasMany;

trait RoleRelationship
{
    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'role_id', 'id');
    }
}
