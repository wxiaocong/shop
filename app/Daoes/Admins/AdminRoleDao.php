<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\AdminRole;
use App\Utils\Page;

class AdminRoleDao extends BaseDao
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
        $builder = AdminRole::select();
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where('name', 'like', '%' . $params['search'] . '%');
        }

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }

    /**
     * 分页查询角色
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = AdminRole::select();

        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where('name', 'like', '%' . $params['search'] . '%');
        }

        return $builder->paginate($pageSize, array('*'), 'page', $curPage);
    }

    /**
     * 查询角色
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = AdminRole::select();

        if (array_key_exists('ids', $params)) {
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
     * 根据Id查询角色
     * @param int $id
     *
     * @return App\Models\Admins\AdminRole
     */
    public static function findById($id)
    {
        return AdminRole::find($id);
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
        $builder = AdminRole::where($key, $value);
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
     * 批量删除
     * @param  array(App\Models\Admins\AdminRole) $adminRoles
     *
     * @return boolean
     */
    public static function batchDelete($adminRoles)
    {
        if (!isset($adminRoles) || count($adminRoles) == 0) {
            return true;
        }

        foreach ($adminRoles as $adminRole) {
            //删除角色用户关联
            $adminRole->users()->detach();
            //删除角色权限关联
            $adminRole->rights()->detach();
        }
        $count = AdminRole::destroy($adminRoles->pluck('id')->all());
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * 保存关联权限关系
     * @param  App\Models\Admins\AdminRole $role
     * @param  array $rightIds
     *
     * @return boolean
     */
    public static function saveRoleRights($role, $rightIds)
    {
        if (isset($role->rights) && count($role->rights) > 0) {
            $role->rights()->detach();
        }
        if (count($rightIds) > 0) {
            $role->rights()->attach($rightIds);
        }

        return true;
    }
}
