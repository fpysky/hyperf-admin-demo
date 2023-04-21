<?php

declare(strict_types=1);

namespace App\Services\Merchant;

use App\Model\Merchant;
use App\Model\MerchantCategoryRelation;
use App\Model\MerchantTagRelation;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class MerchantService
{
    #[Inject]
    protected Merchant $merchantModel;

    #[Inject]
    protected MerchantTagRelation $merchantTagModel;

    #[Inject]
    protected MerchantCategoryRelation $merchantCategoryModel;

    /**
     * @param string $categoryId 记录数组
     * @param array $record 记录数组
     * @return int 自增id
     * @author yulu 2023/4/8
     */
    public function create(string $categoryId, array $record): int
    {
        $merchantId = $this->merchantModel->insertGetId($record);
        // todo 不检查分类id 的合法性，如果分类id不存在，那么查询的分类就不对
        $this->addOrUpdateCategory($merchantId, $categoryId);
        return $merchantId;
    }

    /**
     * 检查商户名是否重名.
     * @param string $merchantName 商户名
     * @return bool
     * @author yulu 2023/4/8
     */
    public function hasSameMerchantName(string $merchantName): bool
    {
        return $this->merchantModel->where(['merchant_name' => $merchantName, 'deleted_at' => null])->count() >= 1;
    }

    /**
     * 根据商户名获取id，没有返回0.
     * @param string $merchantName
     * @return int
     * @author yulu 2023/4/8
     */
    public function getIdByMerchantName(string $merchantName): int
    {
        $record = $this->merchantModel->query()->where(['merchant_name' => $merchantName,
            'deleted_at' => null])->select('id')->first();
        if (empty($record)) {
            return 0;
        }
        return $record['id'];
    }

    /**
     * 编辑用户.
     * @param int $id
     * @param array $record
     * @return int
     * @author yulu 2023/4/8
     */
    public function update(int $id, array $record): int
    {
        // 如果分类需要编辑，删除后再新建
        if (isset($record['category_id'])) {
            $this->addOrUpdateCategory($id, $record['category_id']);
            unset($record['category_id']);
        }
        return $this->merchantModel->where(['id' => $id])->update($record);
    }

    /**
     * 新增或编辑分类.
     * @param int $merchantId
     * @param string $categoryId
     * @author yulu 2023/4/8
     */
    public function addOrUpdateCategory(int $merchantId, string $categoryId)
    {
        $categoryIdArr = explode(',', $categoryId);
        // 元素去重，写入重复的分类会报错
        $categoryIdArr = array_unique($categoryIdArr);
        // 批量写入商家分类关联表
        $datas = [];
        // 写入分类前，已有的分类删除，免得写入不成功
        $this->merchantCategoryModel->where(['merchant_id' => $merchantId])->delete();
        foreach ($categoryIdArr as $cateId) {
            array_push($datas, ['merchant_id' => $merchantId, 'category_id' => $cateId]);
        }
        $this->merchantCategoryModel->insert($datas);
    }

    /**
     * 获取商家基本信息.
     * @param int $id
     * @return array
     * @author yulu 2023/4/8
     */
    public function getInfo(int $id): array
    {
        $res = $this->merchantModel->where(['id' => $id, 'deleted_at' => null])
            ->select(['id', 'phone', 'merchant_name as merchantName', 'cover_url as coverUrl', 'recommendation', 'video_url as videoUrl',
                'image_urls as imageUrls', 'business_hour_type as businessHourType', 'business_hours as businessHours',
                'contact', 'area_id', 'business_circle_id as businessCircleId', 'address', 'lnglat',
                'business_license_url as businessLicenseUrl',
                'legal_person_idcard_front_url as legalPersonIdcardFrontUrl',
                'legal_person_idcard_back_url as legalPersonIdcardBackUrl', 'other_qualification_names as otherQalificationNames',
                'other_qualification_urls as otherQalificationUrls'])->first();

        if (! empty($res)) {
            return $res->toArray();
        }
        return [];
    }

    /**
     * 搜索列表页面.
     * @param int $page
     * @param int $pageSize
     * @param string $categoryId
     * @param string $merchantName
     * @param string $areaId
     * @param string $businessCircleId
     * @param string $status
     * @param int $minSalesCount
     * @param int $maxSalesCount
     * @param string $startCreateTime
     * @param string $endCreateTime
     * @return array
     * @author yulu 2023/4/8
     */
    public function searchUserPageList(
        int $page,
        int $pageSize,
        string $categoryId,
        string $merchantName,
        string $areaId,
        string $businessCircleId,
        string $status,
        int $minSalesCount,
        int $maxSalesCount,
        string $startCreateTime,
        string $endCreateTime
    ): array {
        // $categoryId 是字符串，有可能分割后有多个，需要查询
        // $areaId 是字符串，有可能分割后有多个，需要查询
        // $businessCircleId 是字符串，有可能分割后有多个，需要查询
        // $status 是字符串，有可能分割后有多个，需要查询，如果时0，或者没有，表示搜索全部
        $query = $this->merchantModel::query();
        $query = $query->where('merchant.deleted_at', '=', null);
        $filed = [
            'merchant.id',
            'merchant.merchant_name as merchantName',
            'merchant.goods_count as goodsCount',
            'merchant.sales_count as salesCount',
            'merchant.address as address',
            'merchant.status as status',
            'merchant.created_at as createdAt',
        ];
        if ($categoryId) {
            // 拆分成多个，然后用in， 不考虑父id 的情况
            //$categoryIdArr = explode(',', $categoryId);
           //$query->whereIn('merchant.category_id', $categoryIdArr);
            //todo 如果有了，那么就加入过滤了，查询出merchantId的id所有数组。
            //whereIn (merchant.id  ,merchant.ids)

        }
        if ($merchantName) {
            $query->where('merchant.merchant_name', 'like', '%' . $merchantName . '%');
        }
        if ($areaId) {
            // 拆分成多个，然后用in， 不考虑父id 的情况
            $areaIdArr = explode(',', $areaId);
            $query->whereIn('merchant.area_id', $areaIdArr);
        }
        // $businessCircleId
        if ($businessCircleId) {
            $businessCircleIdArr = explode(',', $businessCircleId);
            $query->whereIn('merchant.business_circle_id', $businessCircleIdArr);
        }
        if ($status) {
            $statusIdArr = explode(',', $status);
            // 如果包含0，不查询
            if (! in_array(0, $statusIdArr)) {
                $query->whereIn('merchant.status', $statusIdArr);
            }
        }
        if ($minSalesCount != -1) {
            $query->where('merchant.sales_count', '>=', $minSalesCount);
        }
        if ($maxSalesCount != -1) {
            $query->where('merchant.sales_count', '<=', $maxSalesCount);
        }
        if ($startCreateTime) {
            $query->where('merchant.created_at', '>=', $startCreateTime . ' 00:00:00');
        }
        if ($endCreateTime) {
            $query->where('merchant.created_at', '<=', $endCreateTime . ' 23:59:59');
        }

        $total = $query->count();
        if ($total == 0) {
            return ['total' => 0, 'list' => []];
        }
        $query->select($filed);
        $query->orderBy('merchant.id', 'desc');
        $list = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->get();
        $list = ! empty($list) ? $list->toArray() : [];
        // 新增字段   categoryTxt 需要查询分类获取，先设置位空
        // fields createdAt  format
        // getMerchantCateInfoList(array $merchantIdArr): array
        $idArr = [];
        foreach ($list as $record) {
            array_push($idArr, $record['id']);
        }
        // 处理 分类显示
        $cateInfoList = $this->getMerchantCateInfoList($idArr);
        foreach ($list as &$record) {
             //categoryId
            //categoryTxt
            $record['categoryId'] = '';
            $record['categoryTxt'] = '';
            if(isset($cateInfoList[$record['id']])){
                $cateInfo = $cateInfoList[$record['id']];
                $record['categoryId'] = implode(',', $cateInfo['id']);
                $record['categoryTxt'] = implode(',', $cateInfo['path']);
            }
            $record['createdAt'] = date('Y-m-d H:i', strtotime($record['createdAt']));
        }
        $data = [];
        $data['total'] = $total;
        $data['list'] = $list;
        return $data;
    }

    /**
     * 删除商家.
     * @param int $id 商户id
     * @author yulu 2023/4/8
     */
    public function deleteMerchant(int $id): int
    {
        // 相关联的分类也要删除
        $this->merchantCategoryModel->where(['merchant_id' => $id])->delete();
        return $this->merchantModel->where(['id' => $id])->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 封禁 解封操作.
     * @param int $id
     * @param int $status
     * @return int
     * @author yulu 2023/4/8
     */
    public function updateStatus(int $id, int $status): int
    {
        return $this->merchantModel->where(['id' => $id])->update(['status' => $status]);
    }

    /**
     * 商家展示，比商家信息多一些信息.
     * @param int $id
     * @return array
     * @author yulu 2023/4/8
     */
    public function detailInfo(int $id): array
    {
        $res = $this->merchantModel->where(['merchant.id' => $id, 'merchant.deleted_at' => null])
            ->select(['merchant.id', 'phone', 'merchant_name as merchantName', 'status',
                // 'category_id as categoryId', 'category_id as categoryTxt',
                'cover_url as coverUrl', 'recommendation', 'video_url as videoUrl',
                'image_urls as imageUrls', 'business_hour_type as businessHourType', 'business_hours as businessHours',
                'contact', 'business_circle_id as businessCircleId', 'bc.name as businessCircle',
                'address', 'lnglat',
                'goods_count as goodsCount',
                'sales_count as salesCount',
                'order_count as orderCount',
                'sales_price as salesPrice',
            ])->leftJoin('business_circle as bc', 'merchant.business_circle_id', '=', 'bc.id')
            ->first();

        if (! empty($res)) {
            $record = $res->toArray();
            // 特殊字段的处理
            $cateInfo = $this->getMerchantCateInfo($id);
            $record['categoryId'] = implode(',', $cateInfo['id']);
            $record['categoryTxt'] = implode(',', $cateInfo['path']);

            $record['businessCircle'] = $record['businessCircle'] != null ? $record['businessCircle'] : '';
            //  -null 处理？
            // 销售额，因为保存的是分，所以要除以100
            $record['salesPrice'] = round((int) $record['salesPrice'] / 100, 2);
            $tagList = $this->getTagList($id);
            $record['tagId'] = implode(',', $tagList['id']);
            $record['tagTxt'] = implode(',', $tagList['name']);

            return $record;
        }
        return [];
    }

    /**
     * 配置标签.
     * @param int $id
     * @param string $tagId
     * @return array
     * @author yulu 2023/4/8
     */
    public function addTag(int $id, string $tagId): array
    {
        // todo 需要检测标签的合理性
        $tagIdArr = explode(',', $tagId);
        // 用于批量写入的数据
        $lists = [];
        foreach ($tagIdArr as $metchantTagId) {
            $metchantTagId = (int) $metchantTagId;
            array_push($lists, ['merchant_id' => $id, 'tag_id' => $metchantTagId]);
        }
        // 删除原有的标签  先硬删除
        // 开启事务
        Db::beginTransaction();
        try {
            $this->merchantTagModel->where(['merchant_id' => $id])->delete();
            // 批量写入新标签
            $this->merchantTagModel->insert($lists);
            Db::commit();
            $res = [];
            $res['id'] = $id;
            $res['tagId'] = $tagId;
            // todo 返回标签的名称概念，暂时不返回
            $res['tagName'] = '';
            return $res;
        } catch (\Exception $ex) {
            Db::rollBack();
            return []; // 返回空数组表示失败
        }
    }

    /**
     * 获取商家的标签 id数组，标签名称数组   按照添加顺序排列.
     * @param int $merchantId
     * @return array ['id'=>[1,2,3],'name'=>['美食','经典','快餐']];
     */
    public function getTagList(int $merchantId): array
    {
        $list = $this->merchantTagModel->select(['merchant_tag_relation.tag_id', 'lt.name'])
            ->leftJoin('merchant_tag as lt', 'merchant_tag_relation.tag_id', '=', 'lt.id')
            ->where(['merchant_tag_relation.deleted_at' => null, 'merchant_tag_relation.merchant_id' => $merchantId])
            ->orderBy('merchant_tag_relation.id', 'asc')->get();

        $list = ! empty($list) ? $list->toArray() : [];

        if (empty($list)) {
            return ['id' => [], 'name' => []];
        }
        $idArr = [];
        $nameArr = [];
        foreach ($list as $record) {
            array_push($idArr, $record['tag_id']);
            array_push($nameArr, $record['name']);
        }
        return ['id' => $idArr, 'name' => $nameArr];
    }

    /**
     * 获取商家分类信息.
     * 返回 ['id'=>[1,3,5],'name'=['早餐','上午茶','下午茶'],'path'=>['美食,早餐','美食,上午茶','美食,下午茶'].
     * @param int $merchantId 商家id
     * @return array
     */
    public function getMerchantCateInfo(int $merchantId): array
    {
        $list = $this->merchantCategoryModel->select(['merchant_category_relation.merchant_id',
            'merchant_category.id',
            'category_name', 'path'])
            ->leftJoin('merchant_category', 'merchant_category_relation.category_id', '=', 'merchant_category.id')
            ->where(['merchant_category_relation.merchant_id' => $merchantId])
            ->orderBy('merchant_category_relation.id')->get();
        $list = ! empty($list) ? $list->toArray() : [];

        if (empty($list)) {
            return ['id' => [], 'name' => [], 'path' => []];
        }
        $idArr = [];
        $nameArr = [];
        $pathArr = [];
        foreach ($list as $record) {
            array_push($idArr, $record['id']);
            array_push($nameArr, $record['category_name']);
            array_push($pathArr, $record['path']);
        }
        return ['id' => $idArr, 'name' => $nameArr, 'path' => $pathArr];
    }

    /**
     * 批量获取商家的分类信息,用于列表显示.
     * 返回 ['merchantId'=>['id'=>[1,3,5],'name'=['早餐','上午茶','下午茶'],'path'=>['美食,早餐','美食,上午茶','美食,下午茶']].
     * @param array $merchantIdArr
     * @return array
     */
    public function getMerchantCateInfoList(array $merchantIdArr): array
    {
        $list = $this->merchantCategoryModel->select(['merchant_category_relation.merchant_id',
            'merchant_category.id',
            'category_name as category_name', 'path'])
            ->leftJoin('merchant_category', 'merchant_category_relation.category_id', '=', 'merchant_category.id')
            ->whereIn('merchant_category_relation.merchant_id', $merchantIdArr)
            ->orderBy('merchant_category_relation.id')->get();
        $list = ! empty($list) ? $list->toArray() : [];
        if (empty($list)) {
            return [];
        }
        $returnList = [];
        foreach ($merchantIdArr as $merchantId) {
            $cateList = []; // 单个cateList
            $idArr = [];
            $nameArr = [];
            $pathArr = [];
            foreach ($list as $record) {
                if ($record['merchant_id'] == $merchantId) {
                    // 处理id，name,path
                    array_push($idArr, $record['id']);
                    array_push($nameArr, $record['category_name']);
                    array_push($pathArr, $record['path']);
                }
            }
            $cateList['id'] = $idArr;
            $cateList['name'] = $nameArr;
            $cateList['path'] = $pathArr;
            $returnList[$merchantId] = $cateList;
        }
        return $returnList;
    }
}
