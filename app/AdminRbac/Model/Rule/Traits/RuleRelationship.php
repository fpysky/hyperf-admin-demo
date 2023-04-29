<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Rule\Traits;

use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;

trait RuleRelationship
{
    public function children(): HasMany
    {
        return $this->hasMany(Rule::class, 'parent_id', 'id');
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(Rule::class, 'parent_id', 'id')
            ->where('type',self::TYPE_BUTTON);
    }

    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'rule_id', 'id');
    }

    public function parentRule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'parent_id', 'id');
    }
}
