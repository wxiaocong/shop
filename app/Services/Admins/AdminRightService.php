<?php

namespace App\Services\Admins;

use App\Daoes\Admins\AdminRightDao;
use App\Models\Admins\AdminRight;

class AdminRightService
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
        return AdminRightDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AdminRightDao::findByParams($params);
    }

    /**
     * 根据ID查询
     */
    public static function findById($id)
    {
        return AdminRightDao::findById($id);
    }

    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $rights = AdminRightDao::findByParams(array('ids' => $ids));
        return AdminRightDao::batchDelete($rights);
    }

    /**
     * update
     * @param App\Models\Admins\AdminRight $right
     *
     * @return App\Models\Admins\AdminRight
     */
    public static function update($right)
    {
        $right->updated_at = date('Y-m-d H:i:s');
        return AdminRightDao::save($right, session('adminUser')->id);
    }

    public static function saveOrUpdate($request)
    {
        $id = trimSpace($request->input('id', 0));
        if ($id == 0) {
            $right          = new AdminRight();
            $right->user_id = session('adminUser')->id;
        } else {
            $right = AdminRightDao::findById($id);
            if (!$right) {
                return array(
                    'code'     => 500,
                    'messages' => array('权限不存在'),
                    'url'      => '',
                );
            }
            $right->updated_at = date('Y-m-d H:i:s');
        }

        $right->category_id = trimSpace($request->input('categoryId', 0));
        $right->name        = trimSpace($request->input('name', ''));
        $right->url         = trimSpace($request->input('url', ''));
        $right->action      = trimSpace($request->input('action', ''));
        $right->sort_num    = trimSpace($request->input('sortNum', 1));
        $right->show_menu   = trimSpace($request->input('showMenu', 0));
        $right->description = trimSpace($request->input('description', ''));
        $right              = AdminRightDao::save($right, session('adminUser')->id);
        if (!$right) {
            return array(
                'code'     => 500,
                'messages' => array('权限保存失败'),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('权限保存成功'),
            'url'      => '',
        );
    }
}
