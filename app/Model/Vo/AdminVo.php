<?php

namespace App\Model\Vo;

trait AdminVo
{
    public function getAdminLogo()
    {
        return $this->logo ?: config('admin.default_admin_head_img');
    }
}