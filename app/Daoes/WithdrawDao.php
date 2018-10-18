<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Withdraw;
use App\Utils\DateUtils;
use App\Utils\Page;

class WithdrawDao extends BaseDao
{
    /**
     * 分页查询提现记录
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params) {
        $builder = Withdraw::join('users as u','withdraw.user_id','=','u.id')
            ->offset($pageSize * ($curPage - 1))->limit($pageSize)
            ->select('withdraw.*', 'u.nickname', 'u.realname');
        if (array_key_exists('userId', $params) && $params['userId'] > 0) {
            $builder->where('withdraw.user_id', $params['userId']);
        }
        if (array_key_exists('state', $params) && $params['state'] > 0) {
            $builder->where('withdraw.state', $params['state']);
        }
        if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
            $builder->where('withdraw.created_at', '>=', DateUtils::addDay(0, $params['startDate']));
        }
        if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
            $builder->where('withdraw.created_at', '<', DateUtils::addDay(1, $params['endDate']));
        }
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $search = $params['search'];
            $builder->where(function($query) use ($search){
                $query->where('withdraw.order_sn', 'like', "%{$search}%")
                    ->orWhere('u.nickname','like', "%{$search}%")
                    ->orWhere('u.realname','like', "%{$search}%");
            });
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy('withdraw.'.$key, $value);
            }
        }
        return  new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }
    

    /**
     * 提现记录
     */
    public static function getAllByUser($user_id, $curPage, $pageSize) {
        return Withdraw::where('user_id', $user_id)->orderBy('id', 'desc')->offset($pageSize * ($curPage - 1))->limit($pageSize)->get();
    }

    /**
     * 提现单详情
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getDetail($id) {
        return Withdraw::join('users as u','withdraw.user_id','=','u.id')
            ->where('withdraw.id', $id)
            ->select('withdraw.*', 'u.nickname', 'u.realname')
            ->first();
    }

    /**
     * 提现单审核
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function audit($id) {
        return Withdraw::where(['id'=>$id, 'state'=>1])->update(['state'=>2, 'pay_time'=>date('Y-m-d H:i:s')]);
    }

    public static function cancle($id) {
        return Withdraw::where(['id'=>$id, 'state'=>1])->update(['state'=>3, 'updated_at'=>date('Y-m-d H:i:s')]);
    }

    public static function findById($id) {
        return Withdraw::find($id);
    }
}
