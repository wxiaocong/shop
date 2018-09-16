<?php

namespace App\Services\Admins;

use App\Daoes\Admins\ModelDao;
use App\Models\Admins\Attribute;
use App\Models\Admins\Model;
use App\Services\Admins\AttributeService;
use DB;

class ModelService
{

    /**
     * 根据id查询模型
     * @param  int $id
     *
     * @return App\Models\Admins\Model
     */
    public static function findById($id)
    {
        return ModelDao::findById($id);
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return ModelDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return ModelDao::findByParams($params);
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
        return ModelDao::existColumn($key, $value, $id);
    }

    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $flag = false;

        DB::beginTransaction();

        try {
            $models = ModelDao::findByParams(array('ids' => $ids));
            if (isset($models) && count($models) > 0) {
                foreach ($models as $model) {
                    AttributeService::batchDelete($model->attributes->pluck('id')->all());
                }

                $flag = ModelDao::batchDelete($models->pluck('id')->all());
                if (!$flag) {
                    DB::rollback();

                    return $flag;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return $flag;
    }

    /**
     * 保存
     * @param  int $id
     * @param  string $name
     * @param  array $attributeParams
     *
     * @return [type]
     */
    public static function saveOrUpdate($id, $name, $attributeParams)
    {
        DB::beginTransaction();

        try {
            $model = self::saveModel($id, $name);
            if (!$model) {
                DB::rollback();

                return array(
                    'code'     => 500,
                    'messages' => array('保存模型失败'),
                    'url'      => '',
                );
            }

            $len = count($attributeParams['name']);
            if ($len != count(array_unique($attributeParams['name']))) {
                DB::rollback();

                return array(
                    'code'     => 500,
                    'messages' => array('同一模型下属性名唯一'),
                    'url'      => '',
                );
            }

            $attributeIds = array();
            $attributes   = array();
            for ($i = 0; $i < $len; $i++) {
                $id = $attributeParams['id'][$i];
                if (isset($id) && $id != '') {
                    $attributeIds[] = $id;
                }

                $attribute = self::generateAttribute($attributeParams, $i);
                if (!$attribute) {
                    DB::rollback();

                    return array(
                        'code'     => 500,
                        'messages' => array('保存模型失败'),
                        'url'      => '',
                    );
                }
                $attributes[] = $attribute;
            }

            //批量删除
            if (count($attributeIds) > 0) {
                $delAttributes = AttributeService::findByParams(array('notIn' => array('id' => $attributeIds)));
                if (isset($delAttributes) && count($delAttributes) > 0) {
                    AttributeService::batchDelete($delAttributes->pluck('id')->all());
                }
            }

            ModelDao::saveMany($model, $attributes);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return array(
                'code'     => 500,
                'messages' => array($e->getMessage()),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('保存模型成功'),
            'url'      => '',
        );
    }

    /**
     * 保存
     * @param  int $id
     * @param  string $name
     *
     * @return App\Models\Admins\Model
     */
    private static function saveModel($id, $name)
    {
        if ($id == 0) {
            $model = new Model();
        } else {
            $model = ModelDao::findById($id);
            if (!$model) {
                return null;
            }
        }

        $model->name = $name;
        return ModelDao::save($model, session('adminUser')->id);
    }

    /**
     * 生成属性对象
     * @param  array $attributeParams
     * @param  init $index
     *
     * @return App\Models\Admins\Attribute
     */
    private static function generateAttribute($attributeParams, $index)
    {
        $id = $attributeParams['id'][$index];
        if (isset($id) && $id != '') {
            $attribute = AttributeService::findById($id);
            if (!$attribute) {
                return null;
            }
            $attribute->updated_at = date('Y-m-d H:i:s');
        } else {
            $attribute = new Attribute();
        }

        $attribute->type   = $attributeParams['showType'][$index];
        $attribute->name   = $attributeParams['name'][$index];
        $attribute->value  = rtrim(str_replace('，', ',', $attributeParams['value'][$index]), ',');
        $attribute->search = $attributeParams['isSearch'][$index];
        $attribute->spec   = $attributeParams['isSpec'][$index];

        return $attribute;
    }
}
