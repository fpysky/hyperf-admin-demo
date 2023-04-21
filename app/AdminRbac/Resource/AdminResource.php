<?php

declare(strict_types=1);

namespace App\AdminRbac\Resource;

use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Model\Dept\Dept;
use Hyperf\Resource\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(): array
    {
        /** @var Admin $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'lastLoginIp' => $this->last_login_ip,
            'logo' => $this->logo,
            'deptId' => $this->dept_id,
            'postId' => $this->post_id,
            'lastLoginTime' => $this->getFormattedDateTime($this->last_login_time),
            'dept' => self::deptInfo($this->dept),
            'createTime' => $this->getFormattedCreatedAt(),
            'updateTime' => $this->getFormattedUpdatedAt(),
        ];
    }

    private function deptInfo(?Dept $dept): array
    {
        if ($dept instanceof Dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->name,
            ];
        }

        return ['id' => 0, 'name' => ''];
    }
}
