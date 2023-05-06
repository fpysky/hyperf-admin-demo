<?php

declare(strict_types=1);

namespace App\Resource\Dept;

use App\AdminRbac\Model\Dept\Dept;
use Hyperf\Resource\Json\JsonResource;

class DeptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Dept $this */
        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'status' => $this->status,
            'sort' => $this->sort,
            'name' => $this->name,
            'remark' => $this->remark,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
