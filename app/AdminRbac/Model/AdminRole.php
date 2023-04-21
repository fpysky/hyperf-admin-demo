<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;

/**
 * @property int $id
 * @property int $role_id
 * @property int $admin_id
 */
class AdminRole extends Model
{
    public bool $timestamps = false;

    protected ?string $table = 'admin_role';

    protected array $fillable = [
        'role_id',
        'admin_id',
    ];

    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'admin_id' => 'integer'];
}
