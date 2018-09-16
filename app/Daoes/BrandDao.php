<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Brand;
use App\Utils\Page;

class BrandDao extends BaseDao
{
    /**
     * 更新排序
     * @param int $id
     * @param int $sort
     */
    public static function BrandSort($id, $sort)
    {
        $Brand       = Brand::find($id);
        $Brand->sort = $sort;
        return $Brand->save();
    }

    /**
     * 根据Id查询分类
     * @param int $id
     *
     * @return App\Models\Brand
     */
    public static function findById($id)
    {
        return Brand::find($id);
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
        $builder = Brand::where(array($key => $value, 'state' => 1));
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $brand = $builder->get();
        if (isset($brand) && count($brand) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 分页查询分类
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = Brand::select();

        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where('logo_cname', 'like', '%' . $params['search'] . '%')
                ->orWhere('logo_ename', 'like', '%' . $params['search'] . '%')
                ->orWhere('short_name', 'like', '%' . $params['search'] . '%');
        }
        $builder->orderByRaw('convert(short_name using gbk) asc');

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }

    /**
     * 查询分类
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = Brand::select();
        $builder->orderByRaw('convert(short_name using gbk) asc');

        return $builder->get();
    }
}
