<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\AgentType;
use App\Utils\Page;

class AgentTypeDao extends BaseDao
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
        $builder = AgentType::select();
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->where('type_name', 'like', '%' . $params['search'] . '%');
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
        return AgentType::where('type_name', $name)->first();
    }
    
    public static function getAll()
    {
        return AgentType::get();
    }

    /**
     * 判断某个字段是否已经存在某个值
     * @param  string $key 字段名
     * @param  string $value 字段值
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($name, $id = 0)
    {
        $builder = AgentType::where('type_name', $name);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $param = $builder->get();

        if (count($param) > 0) {
            return true;
        }

        return false;
    }
    
    public static function destroy($id)
    {
        AgentType::destroy($id);
    }
}
