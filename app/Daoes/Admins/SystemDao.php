<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\System;
use App\Utils\Page;

class SystemDao extends BaseDao
{
    public static function findById($id)
    {
        return System::find($id);
    }
    
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
    public static function findByName($name)
    {
        return System::where('name', $name)->select('val')->first();
    }
    
    public static function getAll()
    {
        $res = System::get();
        $result = array();
        foreach ($res as $v) {
            $result[$v->name] = $v->val;
        }
        return $result;
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
    
    public static function destroy($id)
    {
        System::destroy($id);
    }
}
