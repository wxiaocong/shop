<?php

namespace App\Services\Admins;

use App\Daoes\BrandDao;
use App\Daoes\GoodsSpecDao;
use App\Daoes\OrderDao;
use App\Daoes\PayLogsDao;
use App\Daoes\PromotionDao;
use App\Daoes\Users\UserDao;
use App\Daoes\Users\WechatUserDao;

class StatisticalService
{
    /**
     * 查询品牌数
     * @return int
     */
    public static function findBandCount()
    {
        $brands = BrandDao::findByParams(array());
        return isset($brands) ? count($brands) : 0;
    }

    /**
     * 查询sku数
     * boolean $isNumber
     * @return int
     */
    public static function findSkuCount($isNumber = false)
    {
        $params = array();
        if ($isNumber) {
            $params['isNumber'] = $isNumber;
        }

        $skus = GoodsSpecDao::findByParams($params);
        return isset($skus) ? count($skus) : 0;
    }

    /**
     * 查询限时特价商品数
     * @return int
     */
    public static function findPromotionCount()
    {
        $promotions = PromotionDao::findByParams(array());
        return isset($promotions) ? count($promotions) : 0;
    }

    /**
     * 注册用户总数
     * @return int
     */
    public static function findRegisterCount()
    {
        $users = UserDao::findByParams(array());
        return isset($users) ? count($users) : 0;
    }

    /**
     * 微信访问用户总数
     * @return int
     */
    public static function findWeChatAccessCount()
    {
        $weChatUsers = WechatUserDao::findByParams(array());
        return isset($weChatUsers) ? count($weChatUsers) : 0;
    }

    /**
     * 绑定微信用户总数
     * @return int
     */
    public static function findBindWeChatCount()
    {
        $weChatUsers = WechatUserDao::findByParams(array('isBind' => true));
        return isset($weChatUsers) ? count($weChatUsers) : 0;
    }

    /**
     * 根据时间段查询新增用户数
     * string $startDate
     * string $endDate
     * @return int
     */
    public static function findNewUserCount($startDate, $endDate)
    {
        $params = array('startDate' => $startDate, 'endDate' => $endDate);
        $users  = UserDao::findByParams($params);
        return isset($users) ? count($users) : 0;
    }

    /**
     * 根据参数查询订单数
     * array $params
     * @return int
     */
    public static function findOrderCount($params = array())
    {
        $orders = OrderDao::findByParams($params);
        return isset($orders) ? count($orders) : 0;
    }

    /**
     * 根据参数查询订单销售额
     * array $params
     * @return int
     */
    public static function findOrderSaleCount($params = array())
    {
        $saleAmount = 0;

        //查询用户订单支付的记录
        $params['payType'] = config('statuses.payLog.payType.orderPay.code');
        $payLogs           = PayLogsDao::findByParams($params);
        if (isset($payLogs) && count($payLogs) > 0) {
            $saleAmount = array_sum($payLogs->pluck('expense')->all());
            $ids        = $payLogs->pluck('order_id')->all();
            $idsCount   = count($ids);
            $count      = ceil($idsCount / 990);

            $params            = array();
            $params['payType'] = config('statuses.payLog.payType.refund.code');
            $maxCount          = 990;
            for ($i = 0; $i < $count; $i++) {
                if (((990 * $i) - $idsCount) > 0) {
                    $maxCount = $idsCount - (990 * ($i - 1));
                }
                //查询退款记录
                $params['orderIds'] = array_slice($ids, ($i > 0 ? (990 * ($i - 1)) : 0), $maxCount);
                $payLogs            = PayLogsDao::findByParams($params);
                if (isset($payLogs) && count($payLogs) > 0) {
                    $saleAmount -= array_sum($payLogs->pluck('gain')->all());
                }
            }
        }

        return $saleAmount;
    }
}
