<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 
 * @property int $scenario_id 
 * @property int $merchant_id 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at 
 */
class ScenarioMerchant extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'scenario_merchant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'scenario_id', 'merchant_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'scenario_id' => 'integer', 'merchant_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
