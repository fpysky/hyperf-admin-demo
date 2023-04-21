<?php

declare(strict_types=1);

namespace App\Model\Area;

use App\Model\Area\Traits\AreaRelationship;
use App\Model\Area\Traits\AreaRepository;
use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 
 * @property int $pid 
 * @property string $shortname 
 * @property string $name 
 * @property string $merger_name 
 * @property int $level 
 * @property string $pinyin 
 * @property string $code 
 * @property string $zip_code 
 * @property string $first 
 * @property string $lat 
 * @property string $lng 
 * @property int $sort_order 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at
 * @property Area $parent
 */
class Area extends Model
{
    use SoftDeletes;
    use AreaRepository;
    use AreaRelationship;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'area';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['pid', 'name', 'sort_order'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'pid' => 'integer', 'level' => 'integer', 'sort_order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
