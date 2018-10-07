<?php

namespace App\Services;

use App\Daoes\AgentTypeDao;
use App\Models\AgentType;

class AgentTypeService
{
    public static function getAll()
    {
        $agentTypeList = array();
        $res = AgentTypeDao::getAll();
        foreach ($res as $value) {
            $agentTypeList[$value->id] = $value;
        }
        return $agentTypeList;
    }

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
        return AgentTypeDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByName($name)
    {
        return AgentTypeDao::findByName($name);
    }

    /**
     * 根据ID查询
     */
    public static function findById($id)
    {
        return AgentTypeDao::findById($id);
    }

    /**
     * 保存参数
     * @param  App\Models\Admins\AdminUser $adminUser
     *
     * @return App\Models\Admins\AdminUser
     */
    public static function update($param)
    {
        return AgentTypeDao::save($param);
    }

    public static function saveOrUpdate()
    {
        $id         = trimSpace(request('id', 0));
        $type_name  = trimSpace(clean(request('type_name', '')));
        $price      = intval(request('price', '')) * 100;
        $returnMoney= intval(request('returnMoney', '')) * 100;
        $goodsNum   = intval(request('goodsNum', ''));
        if (AgentTypeDao::existColumn($type_name, $id)) {
            return array(
                'code'     => 500,
                'messages' => array('类型已存在'),
                'url'      => '',
            );
        }
        if ($id == 0) {
            $param = new AgentType();
        } else {
            $param = AgentTypeDao::findById($id);
            if (!$param) {
                return array(
                    'code'     => 500,
                    'messages' => array('类型不存在'),
                    'url'      => '',
                );
            }
            $param->updated_at = date('Y-m-d H:i:s');
        }
        $param->type_name = $type_name;
        $param->price = $price;
        $param->returnMoney = $returnMoney;
        $param->goodsNum = $goodsNum;
        $system = AgentTypeDao::save($param, session('adminUser')->id);
        if (!$system) {
            return array(
                'code'     => 500,
                'messages' => array('保存失败'),
                'url'      => '',
            );
        }
        return array(
            'code'     => 200,
            'messages' => array('保存成功'),
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
        return AgentTypeDao::destroy($id);
    }
}
