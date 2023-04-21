<?php
declare(strict_types=1);

namespace App\Services\User;

use App\Model\User;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\DbConnection\Db;

class UserService
{


    /**
     * 分页查找用户列表
     * @param $page
     * @param $pageSize
     * @param $nickname
     * @param $phone
     * @param $status
     * @param $totalSpentStart
     * @param $totalSpentEnd
     * @param $createdStart
     * @param $createdEnd
     * @return LengthAwarePaginatorInterface
     * @author 两只羊 2023/4/6
     * @modifier 两只羊 2023/4/6
     */
    public function searchUserPageList($page, $pageSize, $nickname, $phone, $status, $totalSpentStart, $totalSpentEnd, $createdStart, $createdEnd): LengthAwarePaginatorInterface
    {

        $query = User::query();
        $filed = [
            'user.id',
            'user_wechat.nickname',
            'user_wechat.headimgurl',
            'user.phone',
            'user_total.total_spent as totalSpent',
            'user.status',
            'user_total.total_orders as totalOrders',
            'user.created_at as createdTime'
        ];
        $query->select($filed);

        if ($nickname) {
            $query->where('user_wechat.nickname', 'like', '%' . $nickname . "%");
        }
        if ($phone) {
            $query->where('user.phone', 'like', '%' . $phone . "%");
        }
        if (is_numeric($status)) {
            $query->where('user.status', '=', $status);
        }
        if ($totalSpentStart) {
            $query->where('user_total.total_spent', '>=', $totalSpentStart);
        }
        if ($totalSpentEnd) {
            $query->where('user_total.total_spent', '<=', $totalSpentEnd);
        }
        if ($createdStart) {
            $query->where('user.created_at', '>=', $createdStart);
        }
        if ($createdEnd) {
            $query->where('user.created_at', '<=', $createdEnd);
        }

        $query->orderBy("user.id", "desc");
        $query->leftJoin('user_total', 'user.id', '=', 'user_total.uid');
        $query->leftJoin('user_wechat', 'user.id', '=', 'user_wechat.uid');
        return $query->paginate($pageSize);


    }

    public function getUserInfoById($id)
    {
        $query = User::query();
        $filed = [
            'user.id',
            'user_wechat.nickname',
            'user_wechat.headimgurl',
            'user.phone',
            'user_total.total_spent',
            'user.status',
            'user_total.total_orders',
            'user.created_at',
            'user_currency.points'
        ];
        $query->select($filed);
        $query->where('user.id', '=', $id);
        $query->leftJoin('user_currency', 'user.id', '=', 'user_currency.uid');
        $query->leftJoin('user_total', 'user.id', '=', 'user_total.uid');
        $query->leftJoin('user_wechat', 'user.id', '=', 'user_wechat.uid');
        return $query->first() ?: [];
    }


    public function ban($id): int
    {
        return User::where('id', '=', $id)->update(['status' => User::STATUS_BAN]);
    }

    public function unblocking($id): int
    {
        return User::where('id', '=', $id)->update(['status' => User::STATUS_ACTIVE]);
    }


}
