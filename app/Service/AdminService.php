<?php

namespace App\Service;

use App\Exception\RecordNotFoundException;
use App\Exception\SystemErrException;
use App\Model\Admin;
use App\Model\Dto\AdminDto;
use Hyperf\Collection\Arr;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Db;
use Hyperf\Stringable\Str;

class AdminService
{
    public function changePassword(Admin $admin, string $newPassword): void
    {
        $admin->password = encryptPassword($newPassword);
        $admin->save();
    }

    /**
     * @throws RecordNotFoundException
     */
    public function findByMobileOrFail(string $mobile): Model|Builder|Admin
    {
        try {
            return Admin::query()
                ->where('mobile', $mobile)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('记录不存在');
        }
    }

    public function create(AdminDto $dto): void
    {
        try {
            Db::beginTransaction();

            $admin = new Admin();
            $admin->name = $dto->name;
            $admin->password = encryptPassword($dto->password);
            $admin->status = $dto->status;
            $admin->type = Admin::TYPE_NORMAL;
            $admin->mobile = $dto->mobile;
            $admin->email = $dto->email;
            $admin->saveOrFail();

            $admin->setRole($dto->roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new SystemErrException("管理员添加失败:{$throwable->getMessage()}");
        }
    }

    public function update(Admin $admin,AdminDto $dto): void
    {
        try {
            Db::beginTransaction();

            $admin->name = $dto->name;
            $admin->status = $dto->status;
            $admin->type = Admin::TYPE_NORMAL;
            $admin->mobile = $dto->mobile;
            $admin->email = $dto->email;
            $admin->saveOrFail();

            $admin->setRole($dto->roleIds);

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw new SystemErrException("管理员更新失败:{$throwable->getMessage()}");
        }
    }

    public function delete(array $ids): void
    {
        Admin::query()
            ->whereIn('id', $ids)
            ->delete();
    }

    public function getAdminPaginateList(int $pageSize,$searchOptions = []): LengthAwarePaginatorInterface
    {
        $builder = Admin::query()
            ->with(['adminDept', 'adminRole'])
            ->orderByDesc('id');

        if (Str::length(Arr::get($searchOptions, 'keyword', '')) !== 0) {
            $keyword = $searchOptions['keyword'];
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        return $builder->paginate($pageSize);
    }

    public function changeStatus(array $ids,int $status): void
    {
        Admin::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
    }
}