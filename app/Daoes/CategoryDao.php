<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Category;
use App\Utils\Page;
use Illuminate\Support\Facades\Redis;

class CategoryDao extends BaseDao
{
    /**
     * 查询一级分类列表
     *
     * @return App\Models\Category
     */
    public static function getList($parent_id = 0, $isHomePage = false)
    {
        $builder = Category::where('parent_id', $parent_id);
        if ($isHomePage) {
            $builder->where('state', 1);
        }
        return $builder->orderBy('sort', 'asc')->get();
    }

    /**
     * 判断name-parent_id是否重复
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($name, $parent_id, $id = 0)
    {
        $builder = Category::where(array('name' => $name, 'parent_id' => $parent_id));
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $category = $builder->get();

        if (isset($category) && count($category) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 更新排序
     * @param int $id
     * @param int $sort
     */
    public static function categorySort($id, $sort)
    {
        $category       = Category::find($id);
        $category->sort = $sort;
        $result         = $category->save();
        if ($category->parent_id == 0) {
            Redis::set('firstCategoryList', serialize(self::getList()), 'EX', 3600 * 24);
        }
        return $result;
    }

    /**
     * 根据Id删除分类及其子分类
     * @param int $id
     *
     */
    public static function deleteAllCategory($id)
    {
        $builder = Category::where('id', $id);

        $isFirst = false; //是否一级
        if (Category::where(array('id' => $id, 'parent_id' => 0))->count()) {
            $isFirst = true;
        }
        //二级
        $second = Category::where('parent_id', $id)->get(array('id'));
        if (!empty($second)) {
            $builder->orWhereIn('id', $second);
            //三级
            $third = Category::whereIn('parent_id', $second)->get(array('id'));
            if (!empty($third)) {
                $builder->orWhereIn('id', $third);
            }
        }
        $res = $builder->delete();
        if ($res && $isFirst) {
            //删除一级分类更新缓存
            Redis::set('firstCategoryList', serialize(self::getList()), 'EX', 3600 * 24);
        }
        return $res;
    }

    /**
     * 根据Id查询分类
     * @param int $id
     *
     * @return App\Models\Category
     */
    public static function findById($id)
    {
        return Category::find($id);
    }

    /**
     * 分页查询分类
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = Category::where('parent_id', 0)->orderBy('sort', 'asc')->select();

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }
}
