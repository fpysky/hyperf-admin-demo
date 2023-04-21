<?php

declare(strict_types=1);

namespace App\Dao\BusinessCircle;

use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Model\BusinessCircle\BusinessCircle;

class BusinessCircleDao
{
    public function create(int $areaId, string $name, int $sortOrder): void
    {
        BusinessCircle::query()->create([
            'area_id' => $areaId,
            'name' => $name,
            'sort_order' => $sortOrder,
        ]);
    }

    public function delete(int $id): void
    {
        $businessCircle = BusinessCircle::query()
            ->findOrFail($id);

        try {
            $businessCircle->delete();
        } catch (\Exception) {
            throw new GeneralException(ErrorCode::SERVER_ERROR, '删除失败，请重试');
        }
    }

    public function update(int $id, int $areaId, string $name, int $sortOrder): void
    {
        BusinessCircle::query()
            ->findOrFail($id)
            ->update([
                'area_id' => $areaId,
                'name' => $name,
                'sort_order' => $sortOrder,
            ]);
    }
}
