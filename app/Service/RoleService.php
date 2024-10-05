<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\AdminRole;
use App\Model\Dto\RoleDto;
use App\Model\Role;
use Hyperf\Collection\Arr;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Stringable\Str;

class RoleService
{
    public function create(RoleDto $dto): void
    {
        $role = new Role();
        $role->name = $dto->name;
        $role->desc = $dto->desc;
        $role->sort = $dto->sort;
        $role->status = $dto->status;
        $role->save();
    }

    public function delete(array $ids): void
    {
        Role::query()
            ->whereIn('id', $ids)
            ->delete();
    }

    public function update(RoleDto $dto, Role $role): void
    {
        $role->name = $dto->name;
        $role->desc = $dto->desc;
        $role->sort = $dto->sort;
        $role->status = $dto->status;
        $role->save();
    }

    public function roleIsBindingAdmin(array|int $id): bool
    {
        $query = AdminRole::query();

        if (is_array($id)) {
            $query->whereIn('role_id', $id);
        } else {
            $query->where('role_id', $id);
        }

        return $query->exists();
    }

    public function getPaginateList(int $pageSize, $searchData = []): LengthAwarePaginatorInterface
    {
        $builder = Role::query()
            ->orderBy('sort')
            ->orderByDesc('id');

        if (Str::length(Arr::get($searchData, 'keyword', '')) !== 0) {
            $builder->where('name', 'like', "%{$searchData['keyword']}%");
        }

        return $builder->paginate($pageSize);
    }

    public function changeStatus(array $ids, int $status): void
    {
        Role::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
    }
}
