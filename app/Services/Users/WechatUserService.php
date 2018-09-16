<?php

namespace App\Services\Users;

use App\Daoes\Users\WechatUserDao;

class WechatUserService
{

    /**
     * 根据openid查询微信用户
     * @param  string $phone
     *
     * @return App\Models\Users\WechatUser
     */
    public static function findByOpenid($openid)
    {
        return WechatUserDao::findByOpenid($openid);
    }

    /**
     * 更新访问次数
     * @param unknown $openid
     */
    public static function updateTotalVisit($openid)
    {
        return WechatUserDao::updateTotalVisit($openid);
    }

    /**
     * 保存更新wechat_user
     * @param array $request
     * @param number $id
     */
    public static function saveOrUpdate($request, $id = 0)
    {
        if ($id) {
            return WechatUserDao::findById($id)->update($request);
        } else {
            return WechatUserDao::addWechatUser($request);
        }
    }

    /**
     * 登录更新用户绑定关系
     */
    public static function updateUserBind($openid, $user_id)
    {
        return WechatUserDao::updateUserBind($openid, $user_id);
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
        return WechatUserDao::findByPageAndParams($curPage, $pageSize, $params);
    }
}
