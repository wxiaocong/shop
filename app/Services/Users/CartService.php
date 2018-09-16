<?php

namespace App\Services\Users;

use App\Daoes\Users\CartDao;
use App\Models\Users\Cart;

class CartService
{
    
    public static function add($userId, $goodsSpec, $num)
    {
        $cart = new Cart();
        $cart->user_id = $userId;
        $cart->spec_id = $goodsSpec['id'];
        $cart->num = $num;
        return $cart->save();
    }
    
    /**
     * 购物车更新数量
     * @param unknown $request
     * @return unknown
     */
    public static function update($user_id, $spec_id, $num)
    {
        return CartDao::update($user_id, $spec_id, $num);
    }
    
    /**
     * 获取购物车列表详情数据
     * @param int $user_id
     * @return array
     */
    public static function findByUserId($user_id)
    {
        return CartDao::findByUserId($user_id);
    }
    
    //获取购物车购买信息
    public static function getPurchase($user_id, $spec)
    {
        return CartDao::getPurchase($user_id, $spec);
    }
    
    //删除购物车商品
    public static function delCart($user_id, $spec)
    {
        return CartDao::delCart($user_id, $spec);
    }
}
