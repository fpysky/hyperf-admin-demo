<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 
 * @property string $title 
 * @property string $subtitle 
 * @property string $cover_url 
 * @property int $status 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $deleted_at 
 */
class Scenario extends Model
{
    use SoftDeletes;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'scenario';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'subtitle', 'cover_url', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];
}
