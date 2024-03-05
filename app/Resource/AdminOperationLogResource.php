<?php

namespace App\Resource;

use App\Model\AdminOperationLog;
use Hyperf\Resource\Json\JsonResource;

/** @mixin AdminOperationLog */
class AdminOperationLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'operateTypeZh' => $this->getOperateTypeZh(),
            'method' => $this->method,
            'operateAdmin' => $this->admin_name,
            'operateIp' => $this->operate_ip,
            'operateIpAddress' => '海南省海口市',//todo::假数据
            'operateStatusZh' => $this->getOperateStatusZh(),
            'operatedAt' => $this->getFormattedDateTime($this->operated_at),
        ];
    }
}
