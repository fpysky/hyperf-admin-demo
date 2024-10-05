<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\AdminOperationLog;
use Hyperf\Collection\Arr;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Stringable\Str;

class OperateLogService
{
    public function getPaginateList(int $pageSize, array $searchOptions = []): LengthAwarePaginatorInterface
    {
        $builder = AdminOperationLog::query();

        if (Str::length(Arr::get($searchOptions, 'module')) !== 0) {
            $builder->where('module', 'like', "%{$searchOptions['module']}%");
        }

        if (Str::length(Arr::get($searchOptions, 'operateType')) !== 0) {
            $builder->where('operate_type', "%{$searchOptions['operateType']}%");
        }

        if (Str::length(Arr::get($searchOptions, 'operateAdmin')) !== 0) {
            $builder->where('admin_name', 'like', "%{$searchOptions['operateAdmin']}%");
        }

        if (Str::length(Arr::get($searchOptions, 'operateStatus')) !== 0) {
            if (in_array($searchOptions['operateStatus'], ['成功', '失败'])) {
                $operateStatus = $searchOptions['operateStatus'] == '成功' ? 1 : 0;
                $builder->where('operateStatus', $operateStatus);
            }
        }

        if (Str::length(Arr::get($searchOptions, 'operateTimeStart')) !== 0) {
            $builder->where('operated_at', '>=', $searchOptions['operateTimeStart']);
        }

        if (Str::length(Arr::get($searchOptions, 'operateTimeEnd')) !== 0) {
            $builder->where('operated_at', '<=', $searchOptions['operateTimeEnd']);
        }

        return $builder->orderByDesc('created_at')
            ->paginate($pageSize);
    }
}
