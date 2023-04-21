<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $phone 
 * @property string $merchant_name 
 * @property int $category_id 
 * @property string $category_id_path 
 * @property int $goods_count 
 * @property int $order_count 
 * @property int $sales_price 
 * @property int $sales_count 
 * @property string $cover_url 
 * @property string $recommendation 
 * @property string $video_url 
 * @property string $image_urls 
 * @property int $business_hour_type 
 * @property string $business_hours 
 * @property string $contact 
 * @property int $area_id 
 * @property int $business_circle_id 
 * @property string $address 
 * @property string $lng 
 * @property string $lat 
 * @property string $lnglat 
 * @property int $status 
 * @property string $business_license_url 
 * @property string $legal_person_idcard_front_url 
 * @property string $legal_person_idcard_back_url 
 * @property string $other_qualification_names 
 * @property string $other_qualification_urls 
 * @property string $last_login_time 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Merchant extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'merchant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'category_id' => 'integer', 'goods_count' => 'integer', 'order_count' => 'integer', 'sales_price' => 'integer', 'sales_count' => 'integer', 'business_hour_type' => 'integer', 'area_id' => 'integer', 'business_circle_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
