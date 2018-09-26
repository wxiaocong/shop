<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\System;
use App\Utils\Page;

class SystemDao extends BaseDao
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
        $builder = System::select();
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->where('name', 'like', '%' . $params['search'] . '%')
                    ->orWhere('val', 'like', '%' . $params['search'] . '%');
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
        $builder = System::select();

        if (array_key_exists('ids', $params) && count($params['ids']) > 0) {
            $builder->whereIn('id', $params['ids']);
        }

        return $builder->get();
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
        $builder = System::where($key, $value);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $param = $builder->get();

        if (isset($param) && count($param) > 0) {
            return true;
        }

        return false;
    }
}
