<?php

namespace App\Daoes\Users;

use App\Daoes\BaseDao;
use App\Models\Users\Cart;

class CartDao extends BaseDao
{
    /**
     * 查找用户购物车
     * @param int $user_id
     * @return NULL|array
     */
    public static function findByUserId($user_id)
    {
        $res = Cart::join('goods_spec as s','cart.spec_id','=','s.id')
            ->where('cart.user_id',$user_id)
            ->select('cart.spec_id','cart.num','s.goods_id','s.name','s.number','s.sell_price as price','s.img','s.state','s.deleted_at')
            ->get()->toArray();
        return empty($res) ? NULL : array_column($res, NULL,'spec_id');
    }
    
    /**
     * 购物车更新数量
     * @param unknown $request
     * @return unknown
     */
    public static function update($user_id, $spec_id, $num)
    {
        if ($num > 0) {
            return Cart::where(['user_id'=>$user_id, 'spec_id'=>$spec_id])->increment('num', $num);
        } elseif ($num < 0) {
            $num = -1 * $num;
            return Cart::where(['user_id'=>$user_id, 'spec_id'=>$spec_id])->where('num','>',1)->decrement('num', $num);
        }
    }
    
    //获取购物车购买信息
    public static function getPurchase($user_id, $spec)
    {
        $res = Cart::join('goods_spec as s','cart.spec_id','=','s.id')
            ->where('cart.user_id',$user_id)
            ->whereIn('cart.spec_id', array_keys($spec))
            ->where('s.state',0)
            ->whereNull('s.deleted_at')
            ->select('cart.spec_id','cart.num','s.goods_id','s.name','s.values','s.number','s.sell_price as price','s.img')
            ->get()->toArray();
        return empty($res) ? NULL : array_column($res, NULL,'spec_id');
    }
    
    //删除购物车商品
    public static function delCart($user_id, $spec)
    {
        return Cart::where('user_id',$user_id)->whereIn('spec_id',$spec)->delete();
    }
}
