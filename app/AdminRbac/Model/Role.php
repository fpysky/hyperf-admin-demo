<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $status
 * @property int $order
 * @property string $name
 * @property string $desc
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 */
class Role extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'status',
        'order',
        'name',
        'desc',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'role_id', 'id');
    }

    public static function exitsByName(string $name,int $exceptId = null): bool
    {
        $builder = self::query()->where('name',$name);

        if(!is_null($exceptId)){
            $builder->where('id','!=',$exceptId);
        }

        return $builder->exists();
    }
}
