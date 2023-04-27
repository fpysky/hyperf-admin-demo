<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\RecordNotFoundException;
use Carbon\Carbon;
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

    public static function findFromCacheOrFail($id)
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException();
        }

        return $model;
    }
}
