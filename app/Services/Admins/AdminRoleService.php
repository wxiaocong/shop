<?php

namespace App\Services\Admins;

use App\Daoes\Admins\AdminRoleDao;
use App\Models\Admins\AdminRole;

class AdminRoleService
{

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params = array())
    {
        return AdminRoleDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 分页查询角色
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return AdminRoleDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 查询角色
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AdminRoleDao::findByParams($params);
    }

    /**
     * 根据Id查询角色
     * @param int $id
     *
     * @return App\Models\Admins\AdminRole
     */
    public static function findById($id)
    {
        return AdminRoleDao::findById($id);
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
        return AdminRoleDao::existColumn($key, $value, $id);
    }

    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $adminRoles = AdminRoleDao::findByParams(array('ids' => $ids));
        return AdminRoleDao::batchDelete($adminRoles);
    }

    /**
     * 保存用户
     * @param  App\Models\Admins\AdminRole $adminRole
     *
     * @return App\Models\Admins\AdminRole
     */
    public static function update($adminRole)
    {
        return AdminRoleDao::save($adminRole, session('adminUser')->id);
    }

    public static function saveOrUpdate()
    {
        $rightIds    = request('rightId', array());
        $id          = trimSpace(request('id', 0));
        $name        = trimSpace(clean(request('name', '')));
        $description = trimSpace(clean(request('description', '')));

        if (count($rightIds) == 0) {
            return array(
                'code'     => 500,
                'messages' => array('请关联权限'),
                'url'      => '',
            );
        }
        if (AdminRoleDao::existColumn('name', $name, $id)) {
            return array(
                'code'     => 500,
                'messages' => array('角色名已存在'),
                'url'      => '',
            );
        }
        if ($id == 0) {
            $role = new AdminRole();
        } else {
            $role = AdminRoleDao::findById($id);
            if (!$role) {
                return array(
                    'code'     => 500,
                    'messages' => array('角色不存在'),
                    'url'      => '',
                );
            }
            $role->updated_at = date('Y-m-d H:i:s');
        }

        $role->name        = $name;
        $role->description = $description;
        $role              = AdminRoleDao::save($role, session('adminUser')->id);
        if (!$role) {
            return array(
                'code'     => 500,
                'messages' => array('角色保存失败'),
                'url'      => '',
            );
        }

        //保存关联权限关系
        AdminRoleDao::saveRoleRights($role, $rightIds);

        return array(
            'code'     => 200,
            'messages' => array('角色保存成功'),
            'url'      => '',
        );
    }
}
