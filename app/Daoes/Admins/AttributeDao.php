<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\Attribute;

class AttributeDao extends BaseDao
{
    /**
     * 根据id查询模型
     * @param  int $id
     *
     * @return App\Models\Admins\Attribute
     */
    public static function findById($id)
    {
        return Attribute::find($id);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = Attribute::select();
        if (array_key_exists('notIn', $params)) {
            foreach ($params['notIn'] as $key => $value) {
                $builder->whereNotIn($key, $value);
            }
        }
        return $builder->get();
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids)
    {
        $count = Attribute::destroy($ids);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
