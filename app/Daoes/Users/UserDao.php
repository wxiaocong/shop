<?php

namespace App\Daoes\Users;

use App\Daoes\BaseDao;
use App\Models\Users\User;
use App\Utils\DateUtils;
use App\Utils\Page;
use Illuminate\Support\Facades\DB;

class UserDao extends BaseDao {
    //锁定余额
    public static function lockBalance($amount, $user_id) {
        return DB::update("UPDATE `users` SET lockBalance = lockBalance + $amount where id = $user_id");
    }

    //锁定余额
    public static function unLockBalance($amount, $user_id) {
        return DB::update("UPDATE `users` SET lockBalance = lockBalance - $amount where id = $user_id");
    }

    //提成，金额锁定
    public static function profit($amount, $user_id, $isLock = true) {
        if ($isLock) {
            return DB::update("UPDATE `users` SET balance = balance + ? ,lockBalance = lockBalance + ? where id = ?", [$amount, $amount, $user_id]);
        } else {
            return DB::update("UPDATE `users` SET balance = balance + ? where id = ?", [$amount, $user_id]);
        }
        
    }

    //提现，金额解锁
    public static function withdraw($amount, $user_id) {
        return DB::update("UPDATE `users` SET balance = balance - ? ,lockBalance = lockBalance - ? where id = ?", [$amount, $amount, $user_id]);
    }

    /**
     * 根据openid查询用户
     * @param  string $openid
     *
     * @return App\Models\Users\User
     */
    public static function findByOpenid($openid) {
        $builder = User::where(array('openid' => $openid, 'state' => 1));

        return $builder != null ? $builder->first() : null;
    }

    /**
     * 根据phone查询用户
     * @param  string $phone
     *
     * @return App\Models\Users\User
     */
    public static function findByPhone($phone) {
        $builder = User::where(array('mobile' => $phone, 'state' => 1));

        return $builder != null ? $builder->first() : null;
    }

    public static function addUser($userData) {
        return User::create($userData);
    }

    public static function saveOrUpdate($openid, $userData) {
        return User::updateOrCreate(['openid' => $openid], $userData);
    }

    /**
     * 统计下级会员数量(非游客)
     */
    public static function countLower($user_id) {
        return User::where('referee_id', $user_id)->where('level', '>', 0)->count();
    }

    /**
     * 根据Id查询用户
     * @param int $id
     *
     * @return App\Models\Users
     */
    public static function findById($id) {
        return User::find($id);
    }

    /**
     * 根据Id查询用户及推荐人
     * @param int $id
     *
     * @return App\Models\Users
     */
    public static function findUserAndRefee($user_id) {
        return User::leftJoin('users as s', 'users.referee_id', '=', 's.id')
            ->where('users.id', $user_id)
            ->select('users.*', 's.nickname as referee_nickname','s.level as referee_level')->first();
    }

    /**
     * 获取用户
     * @param int $id
     *
     * @return App\Models\Users
     */
    public static function getById($id) {
        return User::where('id', $id);
    }

    /**
     * 查询推荐人级别
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public static function findRefereeLevel($user_id) {
        return User::leftJoin('users as s', 'users.referee_id', '=', 's.id')
            ->where('users.id', $user_id)
            ->select('users.id', 'users.referee_id','s.level')->first()->toArray();
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params) {
        $builder = User::select();
        if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
            $builder->where('created_at', '>=', DateUtils::addDay(0, $params['startDate']));
        }
        if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
            $builder->where('created_at', '<', DateUtils::addDay(1, $params['endDate']));
        }

        return $builder->get();
    }

    /**
     * VIP用户总数
     * @return int
     */
    public static function findVipCount() {
        return User::where('vip',1)->count();
    }

    /**
     * 判断某个字段是否已经存在某个值
     * @param  string $key 字段名
     * @param  string $value 字段值
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($key, $value, $id = 0) {
        $builder = User::where(array($key => $value));
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
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params) {
        if (array_key_exists('isBind', $params) && $params['isBind'] > 0) {
            $builder = User::leftJoin('wechat_users', 'users.id', '=', 'wechat_users.user_id')->select('users.*');
            if ($params['isBind'] == 1) {
                $builder->where('wechat_users.user_id', '>', 0);
            } else {
                $builder->where(function ($query) use ($params) {
                    $query->where('wechat_users.user_id', '=', 0)
                        ->orWhereNull('wechat_users.user_id');
                });
            }
        } else {
            $builder = User::select();
        }

        if (array_key_exists('isBusiness', $params) && $params['isBusiness'] > 0) {
            if ($params['isBusiness'] == 1) {
                $builder->where('users.is_business', '=', 1);
            } else {
                $builder->where('users.is_business', '=', 0);
            }
        }

        if (array_key_exists('level', $params) && $params['level'] >= 0) {
            $builder->where('users.level', '=', $params['level']);
        }

        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->where('users.mobile', 'like', '%' . $params['search'] . '%')
                    ->orWhere('users.nickname', 'like', '%' . $params['search'] . '%');
            });
        }

        if (array_key_exists('businessAuditState', $params) && $params['businessAuditState'] != '') {
            $builder->where('users.business_audit_state', $params['businessAuditState']);
        }

        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }

    /**
     * 获取团队数据
     * @param unknown $type
     */
    public static function getTeam($type, $user_id = 0)
    {
        $build =  User::leftJoin('agent', function($leftJoin){
            $leftJoin->on('users.id', '=', 'agent.user_id')->where('agent.state', 3);
        })->where('users.referee_id', $user_id > 0 ? $user_id : session('user')->id)
        ->select('users.*', 'agent.level as agentLevel');
        if ($type > 0) {
            $build->where('users.level', $type-1);
        }
        return $build->orderBy('users.id', 'desc')->get();
    }
}
