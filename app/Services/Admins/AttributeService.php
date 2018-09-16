<?php

namespace App\Services\Admins;

use App\Daoes\Admins\AttributeDao;

class AttributeService
{

    /**
     * 根据id查询
     * @param  int $id
     *
     * @return App\Models\Admins\Attribute
     */
    public static function findById($id)
    {
        return AttributeDao::findById($id);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AttributeDao::findByParams($params);
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids)
    {
        return AttributeDao::batchDelete($ids);
    }
}
