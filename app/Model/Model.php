<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\GeneralException;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 */
abstract class Model extends BaseModel implements CacheableInterface
{
    use Cacheable;

    public function getFormattedCreatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->getFormattedDateTime($this->created_at, $format);
    }

    public function getFormattedUpdatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->getFormattedDateTime($this->updated_at, $format);
    }

    public function getFormattedDeletedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->getFormattedDateTime($this->deleted_at, $format);
    }

    public function getFormattedDateTime($dateTime, string $format = 'Y-m-d H:i:s'): string
    {
        if ($dateTime instanceof Carbon) {
            return $dateTime->format($format);
        }

        if (is_string($dateTime) && ! empty($dateTime)) {
            return Carbon::parse($dateTime)->format($format);
        }

        if (is_numeric($dateTime) && $dateTime != 0) {
            return Carbon::createFromTimestamp($dateTime)->format($format);
        }

        return '';
    }

    /**
     * 获取创建时间的unix时间戳.
     * @return int
     * @author fengpengyuan 2023/2/21
     * @modifier fengpengyuan 2023/2/21
     */
    public function getUnixCreatedAt(): int
    {
        return $this->getUnixTimestamp($this->created_at);
    }

    /**
     * 获取更新时间的unix时间戳.
     * @return int
     * @author fengpengyuan 2023/2/21
     * @modifier fengpengyuan 2023/2/21
     */
    public function getUnixUpdatedAt(): int
    {
        return $this->getUnixTimestamp($this->updated_at);
    }

    /**
     * 获取删除时间的unix时间戳.
     * @return int
     * @author fengpengyuan 2023/2/21
     * @modifier fengpengyuan 2023/2/21
     */
    public function getUnixDeletedAt(): int
    {
        return $this->getUnixTimestamp($this->deleted_at);
    }

    /**
     * 获取Unix时间戳.
     * @param $time
     * @return int
     * @author fengpengyuan 2023/2/21
     * @modifier fengpengyuan 2023/2/21
     */
    public function getUnixTimestamp($time): int
    {
        if (is_int($time)) {
            return $time;
        }

        if ($time instanceof Carbon) {
            return $time->getTimestamp();
        }

        if (is_string($time) && ! empty($time)) {
            return Carbon::parse($time)->getTimestamp();
        }

        return 0;
    }

    /**
     * @param array $simpleQueries
     * @param string[] $selects
     * @return Builder
     * @note 构造查询构造器
     * @author fengpengyuan 2021/10/30
     * @email py_feng@juling.vip
     * @modifier fengpengyuan 2021/10/30
     */
    public function getBuilderBySimpleQueries(array $simpleQueries,$selects = ['*']): Builder
    {
        $builder = self::query()
            ->select($selects);
        if (! empty($simpleQueries)) {
            foreach ($simpleQueries as $index => $simpleQuery){
                if(isset($simpleQuery[1]) && ($simpleQuery[1] === 'in' || $simpleQuery[1] === 'notIn')){
                    if(is_string($simpleQuery[2])){
                        $ids = explode(',',$simpleQuery[2]);
                    }else if(is_array($simpleQuery[2])){
                        $ids = $simpleQuery[2];
                    }else{
                        throw new GeneralException('查询参数错误');
                    }

                    if($simpleQuery[1] === 'in'){
                        $builder->whereIn($simpleQuery[0],$ids);
                    }else if($simpleQuery[1] === 'notIn'){
                        $builder->whereNotIn($simpleQuery[0],$ids);
                    }else{
                        throw new GeneralException('查询参数错误');
                    }

                    unset($simpleQueries[$index]);
                }
            }
            $builder->where($simpleQueries);
        }
        return $builder;
    }
}
