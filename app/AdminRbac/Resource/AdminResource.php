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
            'deptIds' => $this->deptIds(),
            'roleIds' => $this->roleIds(),
            'postId' => $this->post_id,
            'lastLoginTime' => $this->getFormattedDateTime($this->last_login_time),
            'createdAt' => $this->getFormattedCreatedAt(),
            'updatedAt' => $this->getFormattedUpdatedAt(),
        ];
    }
}
