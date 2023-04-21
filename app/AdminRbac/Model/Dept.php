<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $parent_id
 * @property int $status
 * @property int $order
 * @property string $name
 * @property string $mark
 * @property string $username
 * @property string $email
 * @property string $mobile
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 */
class Dept extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'dept';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'parent_id',
        'status',
        'order',
        'name',
        'mark',
        'username',
        'email',
        'mobile',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'status' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function exitsByName(string $name,int $exceptId = null): bool
    {
        $builder = self::query()->where('name',$name);

        if(!is_null($exceptId)){
            $builder->where('id','!=',$exceptId);
        }

        return $builder->exists();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id', 'id');
    }
}
