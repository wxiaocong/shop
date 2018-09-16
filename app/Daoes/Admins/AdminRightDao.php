<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\AdminRight;
use App\Utils\Page;

class AdminRightDao extends BaseDao
{
    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params)
    {
        $builder = AdminRight::select('admin_rights.*')
            ->leftJoin('admin_rights_categories', 'admin_rights.category_id', '=', 'admin_rights_categories.id');

        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where('admin_rights.name', 'like', '%' . $params['search'] . '%');
        }
        if (array_key_exists('categoryId', $params) && $params['categoryId'] != 0) {
            $builder->where(function ($query) use ($params) {
                $query->where('admin_rights_categories.id', $params['categoryId'])
                    ->orWhere('admin_rights_categories.parent_id', $params['categoryId']);
            });
        }

        $builder->orderBy('admin_rights_categories.sort_num', 'asc')
            ->orderBy('admin_rights.sort_num', 'asc');

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = AdminRight::select();

        if (array_key_exists('ids', $params) && count($params['ids']) > 0) {
            $builder->whereIn('id', $params['ids']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }

        return $builder->get();
    }

    /**
     * 根据Id查询
     * @param int $id
     *
     * @return App\Models\AdminRight
     */
    public static function findById($id)
    {
        return AdminRight::find($id);
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
        $builder = AdminRight::where($key, $value);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $rights = $builder->get();

        if (isset($rights) && count($rights) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 批量删除
     * @param  array(App\Models\Admins\AdminRight) $rights
     *
     * @return boolean
     */
    public static function batchDelete($rights)
    {
        if (!isset($rights) || count($rights) == 0) {
            return true;
        }

        foreach ($rights as $right) {
            $right->roles()->detach();
        }
        $count = AdminRight::destroy($rights->pluck('id')->all());
        if ($count > 0) {
            return true;
        }

        return false;
    }
}
