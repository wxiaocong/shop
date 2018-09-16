<?php

namespace App\Daoes\Users;

use App\Daoes\BaseDao;
use App\Models\Users\WechatUser;
use App\Utils\Page;

class WechatUserDao extends BaseDao
{
    /**
     * 根据openid查询wechat用户
     * @param  string $openid
     *
     * @return App\Models\Users\WechatUser
     */
    public static function findByOpenid($openid)
    {
        return WechatUser::where('openid', $openid)->first();
    }

    /**
     * 更新访问次数
     * @param unknown $openid
     */
    public static function updateTotalVisit($openid)
    {
        return WechatUser::where('openid', $openid)->increment('total_visit');
    }

    public static function findById($id)
    {
        return WechatUser::find($id);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = WechatUser::select();
        if (array_key_exists('isNotNull', $params)) {
            foreach ($params['isNotNull'] as $value) {
                $builder->whereNotNull($value);
            }
        }
        if (array_key_exists('isBind', $params)) {
            if ($params['isBind']) {
                $builder->where('user_id', '>', 0);
            } else {
                $builder->where('user_id', '=', 0);
            }
        }

        return $builder->get();
    }

    public static function addWechatUser($wechatUserData)
    {
        return WechatUser::create($wechatUserData);
    }

    /**
     * 登录更新用户绑定关系
     */
    public static function updateUserBind($openid, $user_id)
    {
        $updateData = array(
            'user_id'   => $user_id,
            'last_ip'   => getRealIp(),
            'bind_time' => date('Y-m-s H:i:s'),
            'last_time' => date('Y-m-s H:i:s'),
        );
        return WechatUser::where('openid', $openid)->update($updateData);
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
        $builder = WechatUser::select();
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->orWhere('nickname', 'like', '%' . $params['search'] . '%');
            });
        }
        if (array_key_exists('isBind', $params) && $params['isBind'] > 0) {
            if ($params['isBind'] == 1) {
                $builder->where('user_id', '>', 0);
            } else {
                $builder->where('user_id', '=', 0);
            }
        }

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }
}
