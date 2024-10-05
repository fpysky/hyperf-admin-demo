<?php

namespace App\Model\Vo;

trait AdminOperationLogVo
{
    public function getOperateTypeZh(): string
    {
        switch ($this->operate_type) {
            case 1:
                $str = '新增';
                break;
            case 2:
                $str = '删除';
                break;
            case 3:
                $str = '修改';
                break;
            default:
            case 4:
                $str = '查询';
                break;
        }

        return $str;
    }

    public function getOperateStatusZh(): string
    {
        return $this->operate_status === 1 ? '成功' : '失败';
    }
}