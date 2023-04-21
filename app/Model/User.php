<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $phone 
 * @property int $status 
 * @property string $last_login_time 
 * @property string $nickname 
 * @property int $sex 
 * @property string $headimgurl 
 * @property int $city_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class User extends Model
{
    const STATUS_BAN = 0;//封禁
    const STATUS_ACTIVE = 1;//正常
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'sex' => 'integer', 'city_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    public function searchPageList(){

    }

}
