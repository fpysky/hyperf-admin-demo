<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $order
 * @property string $mark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 */
class Post extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'post';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name',
        'status',
        'order',
        'mark',
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

    public static function exitsByName(string $name,int $exceptId = null): bool
    {
        $builder = self::query()->where('name',$name);

        if(!is_null($exceptId)){
            $builder->where('id','!=',$exceptId);
        }

        return $builder->exists();
    }
}
