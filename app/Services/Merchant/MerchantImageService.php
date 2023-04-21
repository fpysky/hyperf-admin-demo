<?php

declare(strict_types=1);

namespace App\Services\Merchant;

use App\Model\MerchantImage;
use Hyperf\Di\Annotation\Inject;

class MerchantImageService
{
    #[Inject]
    protected MerchantImage $imageModel;

    /**
     * 创建图片.
     * @param int $merchantId
     * @param string $imageUrl
     * @return int
     * @author yulu 2023/4/9
     */
    public function create(int $merchantId, string $imageUrl): int
    {
        return $this->imageModel->insertGetId([
            'merchant_id' => $merchantId,
            'image_url' => $imageUrl,
        ]);
    }

    /**
     * 删除图片.
     * @param int $id
     * @return int
     * author yulu 2023/4/9
     */
    public function delete(int $id): int
    {
        return $this->imageModel->where(['id' => $id])->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 图片列表.
     * @param int $page
     * @param int $pageSize
     * @param int $merchantId
     * @return array
     * author yulu 2023/4/9
     */
    public function searchPageList(int $page, int $pageSize, int $merchantId): array
    {
        $query = $this->imageModel::query();
        $query = $query->where('merchant_image.deleted_at', '=', null);
        $query = $query->where('merchant_image.merchant_id', '=', $merchantId);

        $filed = [
            'merchant_image.id',
            'merchant_image.image_url as imageUrl',
            'merchant_image.created_at as createdAt',
        ];

        $total = $query->count();
        if ($total == 0) {
            return ['total' => 0, 'list' => []];
        }
        $query->select($filed);
        // todo 排序待优化
        $query->orderBy('merchant_image.id', 'desc');

        $list = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->get();
        $list = ! empty($list) ? $list->toArray() : [];
        // fields createdAt  format
        foreach ($list as &$record) {
            $record['createdAt'] = date('Y-m-d H:i', strtotime($record['createdAt']));
        }
        $data = [];
        $data['total'] = $total;
        $data['list'] = $list;
        return $data;
    }
}
