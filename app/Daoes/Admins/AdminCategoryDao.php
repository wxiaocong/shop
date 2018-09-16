<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\AdminRightCategory;

class AdminCategoryDao extends BaseDao
{
    /**
     * 根据Id查询分类
     * @param int $id
     *
     * @return App\Models\Admins\AdminRightCategory
     */
    public static function findById($id)
    {
        return AdminRightCategory::find($id);
    }

    /**
     * 判断某个字段是否已经存在某个值
     * @param  string $key 字段名
     * @param  string $value 字段值
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($key, $value, $id = 0)
    {
        $builder = AdminRightCategory::where($key, $value);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $roles = $builder->get();

        if (isset($roles) && count($roles) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 分页查询分类
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = AdminRightCategory::select();

        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where('name', 'like', '%' . $params['search'] . '%');
        }

        $builder->orderBy('sort_num', 'asc');

        return $builder->paginate($pageSize, array('*'), 'page', $curPage);
    }

    /**
     * 查询分类
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = AdminRightCategory::select();

        if (array_key_exists('ids', $params) && count($params['ids']) > 0) {
            $builder->whereIn('id', $params['ids']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }
        if (array_key_exists('showMenu', $params) && $params['showMenu'] != '') {
            $builder->where('show_menu', $params['showMenu']);
        }
        if (array_key_exists('parentId', $params)) {
            $builder->where('parent_id', $params['parentId']);
        }
        if (array_key_exists('isNotNull', $params)) {
            foreach ($params['isNotNull'] as $value) {
                $builder->whereNotNull($value);
            }
        }
        if (array_key_exists('isNull', $params)) {
            foreach ($params['isNull'] as $value) {
                $builder->whereNull($value);
            }
        }

        return $builder->get();
    }
}
