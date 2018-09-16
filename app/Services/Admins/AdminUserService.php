<?php

namespace App\Services\Admins;

use App\Daoes\Admins\AdminUserDao;
use App\Models\Admins\AdminUser;
use Hash;
use RSA;

class AdminUserService
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
        return AdminUserDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AdminUserDao::findByParams($params);
    }

    /**
     * 根据ID查询
     */
    public static function findById($id)
    {
        return AdminUserDao::findById($id);
    }

    /**
     * 根据name查询用户
     * @param  string $name
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function findByName($name)
    {
        return AdminUserDao::findByName($name);
    }

    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $adminUsers = AdminUserDao::findByParams(array('ids' => $ids));
        return AdminUserDao::batchDelete($adminUsers);
    }

    /**
     * 保存用户
     * @param  App\Models\Admins\AdminUser $adminUser
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function update($adminUser)
    {
        $id = $adminUser->id;
        if (session('adminUser')) {
            $id = session('adminUser')->id;
        }
        return AdminUserDao::save($adminUser, $id);
    }

    public static function saveOrUpdate()
    {
        $password = trimSpace(clean(request('password', '')));
        $password = RSA::decrypt($password);
        $roleIds  = request('roleId', array());
        $id       = trimSpace(request('id', 0));
        $name     = trimSpace(clean(request('name', '')));

        if (count($roleIds) == 0) {
            return array(
                'code'     => 500,
                'messages' => array('至少关联一个角色'),
                'url'      => '',
            );
        }
        if (AdminUserDao::existColumn('admin_name', $name, $id)) {
            return array(
                'code'     => 500,
                'messages' => array('用户名已存在'),
                'url'      => '',
            );
        }
        if ($id == 0) {
            $user = new AdminUser();
        } else {
            $user = AdminUserDao::findById($id);
            if (!$user) {
                return array(
                    'code'     => 500,
                    'messages' => array('管理员不存在'),
                    'url'      => '',
                );
            }
            //id等于1的管理员,只能自己操作,其他人不能编辑
            if ($user->id == 1 && $user->id != session('adminUser')->id) {
                return array(
                    'code'     => 500,
                    'messages' => array('该管理员您无权限编辑'),
                    'url'      => '',
                );
            }
            $user->updated_at = date('Y-m-d H:i:s');
        }
        if (($id == 0 && ($password == '' || strlen($password) < 6)) ||
            ($id != 0 && $password != '' && strlen($password) < 6)) {
            return array(
                'code'     => 500,
                'messages' => array('密码不能少于6个字符'),
                'url'      => '',
            );
        }

        if ($password != '') {
            $user->password = Hash::make(env('ADMIN_PASSWORD_SALT') . $password);
        }
        $user->admin_name = $name;
        $user->email      = trimSpace(clean(request('email', '')));
        $user->qq         = trimSpace(clean(request('qq', '')));
        $user->we_chat    = trimSpace(clean(request('wechat', '')));
        $user->phone      = trimSpace(clean(request('phone', '')));
        $user             = AdminUserDao::save($user, session('adminUser')->id);
        if (!$user) {
            return array(
                'code'     => 500,
                'messages' => array('管理员保存失败'),
                'url'      => '',
            );
        }

        //保存关联角色关系
        AdminUserDao::saveUserRoles($user, $roleIds);

        return array(
            'code'     => 200,
            'messages' => array('管理员保存成功'),
            'url'      => '',
        );
    }
}
