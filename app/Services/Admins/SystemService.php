<?php

namespace App\Services\Admins;

use App\Daoes\Admins\SystemDao;
use App\Models\Admins\System;

class SystemService
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
        return SystemDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByName($name)
    {
        return SystemDao::findByName($name);
    }

    /**
     * 根据ID查询
     */
    public static function findById($id)
    {
        return SystemDao::findById($id);
    }

    /**
     * 保存参数
     * @param  App\Models\Admins\AdminUser $adminUser
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function update($param)
    {
        return SystemDao::save($param);
    }

    public static function saveOrUpdate()
    {
        $id       = trimSpace(request('id', 0));
        $name     = trimSpace(clean(request('name', '')));
        $val     = trimSpace(clean(request('val', '')));
        $desc     = trimSpace(clean(request('desc', '')));
        if (SystemDao::existColumn('name', $name, $id)) {
            return array(
                'code'     => 500,
                'messages' => array('参数已存在'),
                'url'      => '',
            );
        }
        if ($id == 0) {
            $param = new System();
        } else {
            $param = SystemDao::findById($id);
            if (!$param) {
                return array(
                    'code'     => 500,
                    'messages' => array('参数不存在'),
                    'url'      => '',
                );
            }
            $param->updated_at = date('Y-m-d H:i:s');
        }
        $param->name = $name;
        $param->val = $val;
        $param->desc = $desc;
        $system = SystemDao::save($param, session('adminUser')->id);
        if (!$system) {
            return array(
                'code'     => 500,
                'messages' => array('系统参数保存失败'),
                'url'      => '',
            );
        }
        return array(
            'code'     => 200,
            'messages' => array('系统参数保存成功'),
            'url'      => '',
        );
    }
    
    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($id)
    {
        return SystemDao::destroy($id);
    }
}
