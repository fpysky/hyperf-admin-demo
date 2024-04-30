<?php

declare(strict_types=1);

namespace App\Model\Relationship;

use App\Model\RoleRule;
use Hyperf\Database\Model\Relations\HasMany;

trait RoleRelationship
{
    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'role_id', 'id');
    }
}
