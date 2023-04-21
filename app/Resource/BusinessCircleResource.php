<?php

namespace App\Resource;

use App\Model\Area\Area;
use App\Model\BusinessCircle\BusinessCircle;
use Hyperf\Resource\Json\JsonResource;

class BusinessCircleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var BusinessCircle $this */
        return [
            'id' => $this->id,
            'areaName' => self::getAreaName($this->area),
            'name' => $this->name,
            'merchantNumStr' => '1.3万',//todo::假数据
            'sort' => $this->sort_order,
            'createdAt' => $this->getFormattedCreatedAt('Y-m-d H:i'),
        ];
    }

    private function getAreaName(?Area $city): string
    {
        if(is_null($city)){
            return '';
        }

        if($city->parent instanceof Area){
            return "{$city->parent->name}>{$city->name}";
        }else{
            return $city->name;
        }
    }
}
