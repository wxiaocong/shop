<?php

namespace App\Daoes\Admins;

use App\Daoes\BaseDao;
use App\Models\Admins\Model;
use App\Utils\Page;

class ModelDao extends BaseDao
{
    /**
     * 根据id查询模型
     * @param  int $id
     *
     * @return App\Models\Admins\Model
     */
    public static function findById($id)
    {
        return Model::find($id);
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = Model::select();

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
        $builder = Model::select();

        if (array_key_exists('ids', $params)) {
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
        $builder = Model::where($key, $value);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $models = $builder->get();

        if (isset($models) && count($models) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 保存属性
     * @param  App\Models\Admins\Model $model
     * @param  array(App\Models\Admins\Attribute) $attributes
     *
     * @return array
     */
    public static function saveMany($model, $attributes)
    {
        return $model->attributes()->saveMany($attributes);
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids)
    {
        $count = Model::destroy($ids);
        if ($count > 0) {
            return true;
        }

        return false;
    }
}
