<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\AdminUser;
use App\Utils\Page;

class AdminUserDao extends BaseDao
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
        $builder = AdminUser::select();
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->where('admin_name', 'like', '%' . $params['search'] . '%')
                    ->orWhere('email', 'like', '%' . $params['search'] . '%')
                    ->orWhere('qq', 'like', '%' . $params['search'] . '%')
                    ->orWhere('we_chat', 'like', '%' . $params['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $params['search'] . '%');
            });
        }

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
        $builder = AdminUser::select();

        if (array_key_exists('ids', $params) && count($params['ids']) > 0) {
            $builder->whereIn('id', $params['ids']);
        }

        return $builder->get();
    }

    /**
     * 根据Id查询
     * @param int $id
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function findById($id)
    {
        return AdminUser::find($id);
    }

    /**
     * 根据name查询用户
     * @param  string $name
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function findByName($name)
    {
        $builder = AdminUser::where('admin_name', $name);

        return $builder != null ? $builder->first() : null;
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
        $builder = AdminUser::where($key, $value);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $users = $builder->get();

        if (isset($users) && count($users) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 批量删除
     * @param  array(App\Models\Admins\AdminUser) $adminUsers
     *
     * @return boolean
     */
    public static function batchDelete($adminUsers)
    {
        if (!isset($adminUsers) || count($adminUsers) == 0) {
            return true;
        }

        foreach ($adminUsers as $adminUser) {
            $adminUser->roles()->detach();
        }
        $count = AdminUser::destroy($adminUsers->pluck('id')->all());
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * 保存用户关联角色
     * @param  App\Models\Admins\AdminUser $user
     * @param  array $roleIds
     *
     * @return boolean
     */
    public static function saveUserRoles($user, $roleIds)
    {
        if (isset($user->roles) && count($user->roles) > 0) {
            $user->roles()->detach();
        }
        if (count($roleIds) > 0) {
            $user->roles()->attach($roleIds);
        }

        return true;
    }
}
