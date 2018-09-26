<?php

namespace App\Services;

use App\Daoes\AdPositionDao;
use App\Models\AdPosition;

class AdPositionService
{
    public static function saveOrUpdate($request)
    {
        $id    = intVal($request->input('id', 0));
        $title = trimSpace($request->input('title', ''));
        $img   = trimSpace($request->input('img', ''));
        $url   = trimSpace($request->input('url', ''));

        $adPosition = AdPositionDao::findById($id);
        if (!$adPosition) {
            $adPosition = new AdPosition();
        }
        $adPosition->title = $title;
        $adPosition->img   = $img;
        $adPosition->url   = $url;

        $adPosition = AdPositionDao::save($adPosition, session('adminUser')->id);
        if (!$adPosition) {
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
    public static function destroy($ids)
    {
        return AdPositionDao::destroy($ids);
    }

    /**
     * 根据Id查询
     * @param int $id
     *
     * @return App\Models\AdPosition
     */
    public static function findById($id)
    {
        return AdPositionDao::findById($id);
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
        return AdPositionDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return AdPositionDao::findByParams($params);
    }
}
