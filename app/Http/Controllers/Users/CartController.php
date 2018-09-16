<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Daoes\GoodsSpecDao;
use App\Services\Users\CartService;
use App\Services\Users\ExpressAddressService;

class CartController extends Controller
{
    public function index()
    {
        if (empty(session('user')->id)) {
            return redirect('/login');
        }
        $data['cart'] = CartService::findByUserId(session('user')->id);
        $cartNum = empty($data['cart']) ? 0 : array_sum(array_column($data['cart'], 'num'));
        setcookie('cartNum', $cartNum,time()+3600*24,'/');
        return view('users.cart', $data);
    }
    
    //购物车确认订单
    public function purchase()
    {
        if (empty(session('user')->id)) {
            return redirect('/login');
        }
        $data['spec'] = request('spec');
        if (empty($data['spec'])) {
            abort(500, '未选择商品');
        }
        //商品详情
        $data['goods'] = CartService::getPurchase(session('user')->id, $data['spec']);
        if (empty($data['goods'])){
            abort(500, '商品已下架或库存不足');
        }
        //订单数据,删除下架或库存不足的
        if (count($data['goods']) != count($data['spec'])) {
            $tmpSpec = array_keys($data['goods']);
            foreach ($data['spec'] as $key=>$val) {
                if(! in_array($key, $tmpSpec)) {
                    unset($data['spec'][$key]);
                }
            }
        }
        //计算总价格、数量
        $data['totalNum'] = $data['totalPrice'] = 0;
        foreach ($data['goods'] as $good) {
            if($good['num'] > $good['number']) {
                abort(500, $good['name'].' 库存不足');
            }
            $data['totalNum'] += $good['num'];
            $data['totalPrice'] += $good['num'] * $good['price'];
        }
        //默认地址
        $data['defaultAddress'] = ExpressAddressService::getDefault();
        return view('users.cartPurchase', $data);
    }
    
    //添加购物车,购物车统一采用原价
    public function store()
    {
        if (! empty(session('user')->id)) {
            $userId = session('user')->id;
            
            $goodsId = intval(request('goods_id',0));
            $specId = intval(request('spec_id',0));
            $num = intval(request('num',0));
            $spec = request('spec');
            if (empty($goodsId) || (empty($spec) && empty($specId)) || empty($num)) {
                return response()->json(NULL);
            }
            $goodsSpec = GoodsSpecDao::getSpec($goodsId, $spec, $specId);
            //获取购物车数据
            $cart = CartService::findByUserId($userId);
            if (empty($cart[$goodsSpec['id']])) {
                //购物车没有，新增
                $res = CartService::add($userId,$goodsSpec,$num);
            } else {
                //增加数量
                $res = CartService::update($userId, $goodsSpec['id'], $num);
            }
            if ($res) {
                //更新购物车cookie
                $cartNum = $_COOKIE['cartNum'] + $num;
                setcookie('cartNum', $cartNum, time()+3600*24,'/');
                return response()->json(
                    array(
                        'code'     => 200,
                        'messages' => array('加入购物车成功'),
                        'data'     => $cartNum
                    )
                );
            } else {
                return response()->json(
                    array(
                        'code'     => 500,
                        'messages' => array('未更新购物车'),
                    )
                );
            }
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('登录超时,请重新登录'),
                )
            );
        }
    }
    
    //删除购物车商品
    public function delCart()
    {
        if (! empty(session('user')->id)) {
            $spec = request('spec');
            if (empty($spec)) {
                return response()->json(
                    array(
                        'code'     => 500,
                        'messages' => array('请选择要删除的商品'),
                    )
                );
            }
            if(CartService::delCart(session('user')->id, $spec)) {
                //更新购物车cookie
                $cart = CartService::findByUserId(session('user')->id);
                $cartNum = empty($cart) ? 0 : array_sum(array_column($cart, 'num'));
                setcookie('cartNum', $cartNum, time()+3600*24,'/');
                return response()->json(
                    array(
                        'code'     => 200,
                        'messages' => array('删除购物车商品成功'),
                        'data'     => $cartNum
                    )
                );
            } else {
                return response()->json(
                    array(
                        'code'     => 500,
                        'messages' => array('没有购物车商品删除')
                    )
                );
            }
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('登录超时,请重新登录'),
                )
            );
        }
    }
}
