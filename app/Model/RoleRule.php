<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id 
 * @property int $role_id 角色id
 * @property int $rule_id 权限id
 * @property-read null|Rule $rule 
 * @property-read null|Role $role 
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
    protected array $fillable = ['id', 'role_id', 'rule_id'];

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
