<?php

declare(strict_types=1);

namespace App\Services\Merchant;

use App\Model\MerchantVideo;
use Hyperf\Di\Annotation\Inject;

class MerchantVideoService
{
    #[Inject]
    protected MerchantVideo $videoModel;

    /**
     * 创建视频.
     * @param int $merchantId
     * @param string $title
     * @param string $videoUrl
     * @param string $coverUrl
     * @return int
     * @author yulu 2023/4/9
     */
    public function create(int $merchantId, string $title, string $videoUrl, string $coverUrl): int
    {
        return $this->videoModel->insertGetId([
            'merchant_id' => $merchantId,
            'title' => $title,
            'video_url' => $videoUrl, 'cover_url' => $coverUrl]);
    }

    /**
     * 编辑标题.
     * @param int $id
     * @param string $title
     * @return int
     * @author yulu 2023/4/9
     */
    public function editTitle(int $id, string $title): int
    {
        return $this->videoModel->where(['id' => $id])->update(['title' => $title]);
    }

    /**
     * 删除视频.
     * @param int $id
     * @return int
     * author yulu 2023/4/9
     */
    public function delete(int $id): int
    {
        return $this->videoModel->where(['id' => $id])->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 视频列表.
     * @param int $page
     * @param int $pageSize
     * @param int $merchantId
     * @return array
     * author yulu 2023/4/9
     */
    public function searchPageList(int $page, int $pageSize, int $merchantId): array
    {
        $query = $this->videoModel::query();
        $query = $query->where('merchant_video.deleted_at', '=', null);
        $query = $query->where('merchant_video.merchant_id', '=', $merchantId);

        $filed = [
            'merchant_video.id',
            'merchant_video.title',
            'merchant_video.play_count as playCount',
            'merchant_video.cover_url as coverUrl',
            'merchant_video.video_url as videoUrl',
            'merchant_video.created_at as createdAt',
        ];

        $total = $query->count();
        if ($total == 0) {
            return ['total' => 0, 'list' => []];
        }
        $query->select($filed);
        // todo 排序待优化
        $query->orderBy('merchant_video.id', 'desc');

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
