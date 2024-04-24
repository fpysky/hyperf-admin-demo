<?php

declare(strict_types=1);

namespace App\Model\Role;

use App\Exception\RecordNotFoundException;
use App\Model\Origin\Role as Base;
use App\Model\Role\Traits\RoleRelationship;
use Hyperf\Database\Model\SoftDeletes;

class Role extends Base
{
    use SoftDeletes;
    use RoleRelationship;

    /** 状态：启用 */
    const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    const STATUS_DISABLED = 0;

    public static function nameExist(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    /**
     * @throws \Exception
     */
    public function clearRule()
    {
        RoleRule::query()
            ->where('role_id', $this->id)
            ->delete();
    }

    /**
     * @throws \Exception
     */
    public function setRule(array $ruleIds)
    {
        $this->clearRule();

        $insertData = array_map(function ($ruleId){
            return [
                'role_id' => $this->id,
                'rule_id' => $ruleId,
            ];
        },$ruleIds);

        RoleRule::query()->insert($insertData);
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('角色不存在');
        }

        return $model;
    }
}
