<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\AdPosition;
use App\Utils\Page;

class AdPositionDao extends BaseDao
{
    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $count = AdPosition::destroy($ids);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 根据Id查询
     * @param int $id
     *
     * @return App\Models\AdPosition
     */
    public static function findById($id)
    {
        return AdPosition::find($id);
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
        $builder = AdPosition::select();
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
        $builder = AdPosition::select();
        return $builder->get();
    }
}
