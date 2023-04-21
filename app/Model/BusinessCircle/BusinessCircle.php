<?php

declare(strict_types=1);

namespace App\Model\BusinessCircle;

use App\Model\Area\Area;
use App\Model\BusinessCircle\Traits\BusinessCircleRelationship;
use App\Model\BusinessCircle\Traits\BusinessCircleRepository;
use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 
 * @property int $area_id 
 * @property string $name 
 * @property int $sort_order 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 * @property Area $area
 */
class BusinessCircle extends Model
{
    use SoftDeletes;
    use BusinessCircleRepository;
    use BusinessCircleRelationship;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'business_circle';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'area_id', 'name', 'sort_order', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'area_id' => 'integer', 'sort_order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
