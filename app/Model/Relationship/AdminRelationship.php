<?php

declare(strict_types=1);

namespace App\Model\Relationship;

use App\Model\AdminDept;
use App\Model\AdminRole;
use App\Model\Post;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;

trait AdminRelationship
{
    public function adminRole(): HasMany
    {
        return $this->hasMany(AdminRole::class, 'admin_id', 'id');
    }

    public function adminDept(): HasMany
    {
        return $this->hasMany(AdminDept::class, 'admin_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
