<?php

namespace App\Services\Admins;

use App\Daoes\Admins\AdminCategoryDao;

class AdminCategoryService
{

    /**
     * 根据Id查询分类
     * @param int $id
     *
     * @return App\Models\Admins\AdminRightCategory
     */
    public static function findById($id)
    {
        return AdminCategoryDao::findById($id);
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
        return AdminCategoryDao::existColumn($key, $value, $id);
    }

    /**
     * 分页查询分类
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return AdminCategoryDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 查询分类
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AdminCategoryDao::findByParams($params);
    }
}
