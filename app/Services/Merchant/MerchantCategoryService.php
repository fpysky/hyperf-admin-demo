<?php

declare(strict_types=1);

namespace App\Services\Merchant;

use App\Model\MerchantCategory;
use Hyperf\Di\Annotation\Inject;

class MerchantCategoryService
{
    #[Inject]
    protected MerchantCategory $categoryModel;

    /**
     * @param int $parentId
     * @param string $categoryName
     * @param string $iconUrl
     * @param int $sortOrder
     * @return array
     * @author yulu 2023/4/8
     */
    public function create(int $parentId, string $categoryName, string $iconUrl, int $sortOrder): array
    {
        $id = $this->categoryModel->insertGetId([
            'parent_id' => $parentId,
            'category_name' => $categoryName,
            'icon_url' => $iconUrl, 'sort_order' => $sortOrder]);

        // 获取path属性
        $pathInfo = $this->getCatePathInfo($id);
        // 更新属性
        $this->categoryModel->where(['id' => $id])->update($pathInfo);
        // 返回的属性包含 id,path
        return ['id' => $id, 'path' => $pathInfo['path']];
    }

    /**
     * 删除分类.
     * @param int $id
     * @return int
     * @author yulu 2023/4/8
     */
    public function delete(int $id): int
    {

        //如果是三级分类，修改下面的商家
        //二级分类，删除下面的三级分类
        //一级分类，下面的分类都删除
        return $this->categoryModel->where(['id' => $id])->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
        //todo 可能要更新其他表 删除后，下面的往上移动
        //todo 更新商家分类处理
    }

    /**
     * 编辑分类.
     * @param $id
     * @param int $parentId
     * @param string $categoryName
     * @param string $iconUrl
     * @param int $sortOrder
     * @return array
     * @author yulu 2023/4/8
     */
    public function edit($id, int $parentId, string $categoryName, string $iconUrl, int $sortOrder): array
    {
        // 可能会影响其他，非叶子节点
        // 和原有的比较，如果仅仅是 iconUrl变化了，那么就更新 iconUrl即可，别的不用改。否则要做关联修改
        // 原有的查询出来，避免改其他节点
        $oldInfo = $this->categoryModel->where(['id' => $id])->first();
        $changeOthers = false;
        if ($oldInfo) {
            if ($oldInfo['parent_id'] != $parentId) {
                $changeOthers = true;
            }
            if ($oldInfo['category_name'] != $categoryName) {
                $changeOthers = true;
            }
            if ($oldInfo['sort_order'] != $sortOrder) {
                $changeOthers = true;
            }
        }
        $update = ['parent_id' => $parentId,
            'category_name' => $categoryName,
            'icon_url' => $iconUrl, 'sort_order' => $sortOrder];
        $res = $this->categoryModel->where(['id' => $id])->update($update);
        if ($res && $changeOthers) {
            // 修改了，修改其他，先修改其他属性 获取sortPath， 根据，拆分后，获取他的等级
            $cateInfo = $this->getCatePathInfo($id);
            $updOtherRes = $this->categoryModel->where(['id' => $id])->update($cateInfo);
            // 三级分类，只修改自己就行了
            $pathCountArr = explode(',', $cateInfo['sort_path']);
            $pathCount = count($pathCountArr);
            // 数量变化2 ，  6 三级   4 二级    2一级
            if ($pathCount <= 4) {
                // 二级分类，处理下面的三级分类
                $childIdList = $this->categoryModel->select(['id'])->where(['parent_id' => $id, 'deleted_at' => null])->get();

                if (! empty($childIdList)) {
                    $childIdList = $childIdList->toArray();
                    foreach ($childIdList as $record) {
                        $childId = $record['id'];
                        $cateInfo = $this->getCatePathInfo($childId);
                        $this->categoryModel->where(['id' => $childId])->update($cateInfo);

                        // 数量变化2 ，  6 三级   4 二级    2一级
                        if ($pathCount <=2) {
                            // 查询3级分类
                            $grandsonIdList = $this->categoryModel->select(['id'])->where(['parent_id' => $childId, 'deleted_at' => null])->get();
                            if (! empty($grandsonIdList)) {
                                $grandsonIdList = $grandsonIdList->toArray();
                                foreach ($grandsonIdList as $record2) {
                                    $grandSonId = $record2['id'];
                                    $cateInfo = $this->getCatePathInfo($grandSonId);
                                    $this->categoryModel->where(['id' => $grandSonId])->update($cateInfo);
                                }
                            }
                        }
                    }
                }
            }
        }
        $cateInfo = $this->getCatePathInfo($id);
        return ['id' => $id, 'path' => $cateInfo['path']];
    }

    /**
     * 搜索列表.
     * @param int $page
     * @param int $pageSize
     * @param string $categoryName
     * @return array
     * @author yulu 2023/4/8
     */
    public function searchPageList(int $page, int $pageSize, string $categoryName): array
    {
        $query = $this->categoryModel::query();
        $query = $query->where('merchant_category.deleted_at', '=', null);
        //categoryName 模糊搜索
        if ($categoryName) {
            $query->where('merchant_category.path', 'like', '%' . $categoryName . '%');
        }
        $filed = [
            'merchant_category.id',
            'merchant_category.parent_id as parentId',
            'merchant_category.path',
            'merchant_category.category_name as categoryName',
            'merchant_category.icon_url as iconUrl',
            'merchant_category.sort_order as sortOrder',
            'merchant_category.created_at as createdAt',
        ];

        $total = $query->count();
        if ($total == 0) {
            return ['total' => 0, 'list' => []];
        }
        $query->select($filed);
        // todo 排序待优化
        $query->orderBy('merchant_category.sort_path', 'asc');

        $list = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->get();
        $list = ! empty($list) ? $list->toArray() : [];
        // fields createdAt  format
        foreach ($list as &$record) {
            // todo add merchantCount  商户数量
            $record['merchantCount'] = 0; // 暂时先不处理，通过一个批量查询获取，一个服务
            $record['createdAt'] = date('Y-m-d H:i', strtotime($record['createdAt']));
        }
        $data = [];
        $data['total'] = $total;
        $data['list'] = $list;
        return $data;
    }

    /**
     * 选择列表，树形结构，三层
     * @author yulu 2023/4/8
     */
    public function selectList()
    {
        $query = $this->categoryModel::query();
        $query = $query->where('merchant_category.deleted_at', '=', null);
        $filed = [
            'merchant_category.id',
            'merchant_category.parent_id',
            'merchant_category.category_name as name',
            'merchant_category.sort_order',
        ];

        $query->select($filed);
        $query->orderBy('merchant_category.sort_path', 'asc');
        $list = $query->get();
        $returnList = [];
        // 返回一个三级结构
        // 获取parentId=0的内容
        foreach ($list as $record) {
            if ($record['parent_id'] == 0) {
                $firstLevelElement = [];
                $firstLevelElement['id'] = $record['id'];
                $firstLevelElement['name'] = $record['name'];
                $level1List = [];
                // 获取第2级
                foreach ($list as $recordLevel2) {
                    if ($recordLevel2['parent_id'] == $record['id']) {
                        // 加入二级元素
                        $level2Element = [];
                        $level2Element['id'] = $recordLevel2['id'];
                        $level2Element['name'] = $recordLevel2['name'];
                        // 第三季元素
                        $level2List = [];
                        foreach ($list as $recordLevel3) {
                            if ($recordLevel3['parent_id'] == $recordLevel2['id']) {
                                $level3Element = [];
                                $level3Element['id'] = $recordLevel3['id'];
                                $level3Element['name'] = $recordLevel3['name'];
                                array_push($level2List, $level3Element);
                            }
                        }
                        $level2Element['list'] = $level2List;
                        array_push($level1List, $level2Element);
                    }
                }
                $firstLevelElement['list'] = $level1List;
                array_push($returnList, $firstLevelElement);
            }
        }

        return $returnList;
    }

    /**
     * 新增或编辑分类时，传入分类的id和名称，返回  id_path和path,sort_path
     * 对于一级分类，返回如  id_path=> ,1,   path=> 美食, sort_path=>1
     * 对于二级分类，返回如  id_path=> ,1,9,  path=> 美食>炸鸡  sort_path=>1,2
     * 对于三级分类，返回如  id_path=> ,1,9,11   path=>美食>炸鸡>韩式炸鸡  sort_path=>1,2,3.
     * @param $id
     * @return array
     */
    public function getCatePathInfo($id): array
    {
        // 一次性将三级的属性查出来，然后设置后返回
        $cateInfo = $this->categoryModel->select(['merchant_category.id',
            'merchant_category.category_name',
            'merchant_category.sort_order',
            'tb2.id as parent_id',
            'tb2.category_name as parent_name',
            'tb1.id as top_id',
            'tb1.category_name as top_name',
            'tb2.sort_order as parent_order',
            'tb1.sort_order as top_order'])
            ->leftJoin('merchant_category as tb2', 'merchant_category.parent_id', '=', 'tb2.id')
            ->leftJoin('merchant_category as tb1', 'tb2.parent_id', 'tb1.id')
            ->where(['merchant_category.id' => $id,
                'merchant_category.deleted_at' => null,
                'tb2.deleted_at' => null,
                'tb1.deleted_at' => null, ])
            ->first();

        // id_path=,  加上top_id,parent_id,id  如果是null就不加入
        if (empty($cateInfo)) {
            return [];
        }
        $idPath = ',';
        if (! empty($cateInfo['top_id'])) {
            $idPath = $idPath . $cateInfo['top_id'] . ',';
        }
        if (! empty($cateInfo['parent_id'])) {
            $idPath = $idPath . $cateInfo['parent_id'] . ',';
        }
        $idPath = $idPath . $cateInfo['id'] . ',';
        $path = '';
        // path    top_name>parent_name>category_name
        if (! empty($cateInfo['top_name'])) {
            $path = $path . $cateInfo['top_name'] . '>';
        }
        if (! empty($cateInfo['parent_name'])) {
            $path = $path . $cateInfo['parent_name'] . '>';
        }
        $path = $path . $cateInfo['category_name'];

        // sort_path top_order,parent_order,sort_order
        // sort_path   top_order,top_id,parent_order,parent_id,order_sort,id  这样才能排序准确
        $sortPath = '';
        // 判断null
        if (! is_null($cateInfo['top_order'])) {
            $sortPath = $sortPath . $cateInfo['top_order'] . ',';
        }
        if (! is_null($cateInfo['top_id'])) {
            $sortPath = $sortPath . $cateInfo['top_id'] . ',';
        }
        if (! is_null($cateInfo['parent_order'])) {
            $sortPath = $sortPath . $cateInfo['parent_order'] . ',';
        }
        if (! is_null($cateInfo['parent_id'])) {
            $sortPath = $sortPath . $cateInfo['parent_id'] . ',';
        }
        $sortPath = $sortPath . $cateInfo['sort_order'] . ',';
        $sortPath = $sortPath . $cateInfo['id'];

        return ['id_path' => $idPath, 'path' => $path, 'sort_path' => $sortPath];
    }
}
