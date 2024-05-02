<?php

declare(strict_types=1);

namespace App\Model;

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

    /** 默认分页条数 */
    protected int $perPage = 15;

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

    public function getUnixCreatedAt(): int
    {
        return $this->getUnixTimestamp($this->created_at);
    }

    public function getUnixUpdatedAt(): int
    {
        return $this->getUnixTimestamp($this->updated_at);
    }

    public function getUnixDeletedAt(): int
    {
        return $this->getUnixTimestamp($this->deleted_at);
    }

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
}
