<?php

namespace App\Services;

use App\Daoes\CategoryDao;
use App\Models\Category;
use Illuminate\Support\Facades\Redis;

class CategoryService
{

    /**
     * 根据parent_id查询categoryList
     *
     * @return App\Models\Category
     */
    public static function getCategoryList($parent_id = 0)
    {
        if ($parent_id == 0) {
//             Redis::del('firstCategoryList');
            if (!Redis::exists('firstCategoryList')) {
                Redis::set('firstCategoryList', serialize(CategoryDao::getList()), 'EX', 3600 * 24);
            }
            return unserialize(Redis::get('firstCategoryList'));
        }
        return CategoryDao::getList($parent_id);
    }

    /**
     * 跟据分类查询品牌
     */
    public static function getBrandByCategory($category_id)
    {
        return CategoryDao::getBrandByCategory($category_id);
    }

    /**
     * 判断name-parent_id是否重复
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($name, $parent_id, $id = 0)
    {
        $result = CategoryDao::existColumn($name, $parent_id, $id);
        if ($result) {
            return array(
                'code'     => 500,
                'messages' => array('分类名称已存在'),
                'url'      => '',
            );
        }
    }

    /**
     * 保存更新category
     * @param unknown $request
     * @param unknown $id
     */
    public static function saveOrUpdate($request, $id = 0)
    {
        $categoryInfo = null;
        $parent_id = $request['parent_id'];
        if ($id) {
            $categoryInfo = self::findById($id);
            if (!empty($request['good']['pic'][0])) {
                $request['pic'] = '/files/' . $request['good']['pic'][0];
            }
            unset($request['parent_id']);
            $res = $categoryInfo->update($request->all());
        } else {
            $category = new Category();

            $category->name      = $request['name'];
            $category->parent_id = intval($request['parent_id']);
            $category->sort      = $request['sort'];
            $category->state     = $request['state'];
            if (!empty($request['good']['pic'][0])) {
                $category->pic = '/files/' . $request['good']['pic'][0];
            }
            $res = CategoryDao::save($category);
        }
        //一级分类更新缓存
        if ($parent_id == 0 || (isset($categoryInfo['parent_id']) && $categoryInfo['parent_id'] == 0)) {
            Redis::set('firstCategoryList', serialize(CategoryDao::getList()), 'EX', 3600 * 24);
        }
        return $res;
    }

    /**
     * 更新排序
     * @param int $id
     * @param int $sort
     */
    public static function categorySort($id, $sort)
    {
        return CategoryDao::categorySort($id, $sort);
    }

    /**
     * 根据Id删除分类及其子分类
     * @param int $id
     *
     */
    public static function delete($id)
    {
        return CategoryDao::deleteAllCategory($id);
    }

    /**
     * 根据Id查询分类
     * @param int $id
     *
     */
    public static function findById($id)
    {
        return CategoryDao::findById($id);
    }

    /**
     * 分页查询分类
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return CategoryDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 查询分类列表
     *
     * @return array(App\Models\Category)
     */
    public static function findByParentId($parentId = 0)
    {
        return CategoryDao::getList($parentId, true);
    }
}
