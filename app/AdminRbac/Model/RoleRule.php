<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property int $role_id
 * @property int $rule_id
 * @property Role $role
 */
class RoleRule extends Model
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'role_id',
        'rule_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'rule_id' => 'integer'];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id', 'id');
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
