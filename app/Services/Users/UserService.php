<?php

namespace App\Services\Users;

use App\Daoes\Users\UserDao;
use App\Daoes\Admins\SystemDao;
use App\Services\AgentService;
use App\Services\PayLogsService;
use App\Services\WechatNoticeService;
use App\Services\Admins\StatisticalService;
use App\Services\OrderService;

class UserService {
    /**
     * 付款用户及上级级别变更
     * 奖励分配
     */
    public static function upgradeUserLevel($user_id) {
        $userInfo = UserDao::findById($user_id);
        if (!empty($userInfo)) {
            //系统参数
            $system_param = SystemDao::getAll();
            $userUpdateData = array();
            //游客下单付款升级艾达人
            if ($userInfo->level == 0) {
                $userUpdateData['level'] = 1;
            }
            //前9000名VIP，基数1000
            if ($userInfo->vip == 0) {
                $vipCount = StatisticalService::findVipCount();
                if ($vipCount < 9000) {
                    $userUpdateData['vip'] = 1;
                    $userUpdateData['vipNumber'] = $vipCount + 1001;
                }
            }
            if (!empty($userUpdateData)) {
                UserDao::getById($user_id)->update($userUpdateData);
                if(!empty($userUpdateData['vip'])) {
                    //VIP消息提示
                    $template = config('templatemessage.vip');
                    $templateData = array(
                        'first' => '恭喜您成为植得艾VIP专享会员',
                        'keyword1' => $userUpdateData['vipNumber'],
                        'keyword2' => date('Y-m-d'),
                        'remark' => '感谢您的到来',
                    );
                    $url = config('app.url').'/home';
                    WechatNoticeService::sendTemplateMessage($userInfo->id, $userInfo->openid, $url, $template['template_id'], $templateData);
                }
            }
            //上级艾达人如果有5个下级艾达人，升级艾天使
            if ($userInfo->referee_id > 0) {
                //上级信息
                $refereeInfo = UserDao::findById($userInfo->referee_id);
                if (!empty($refereeInfo)) {
                    if($refereeInfo->level == 1) {
                        //统计下级艾达人数量
                        $lowerNum = UserDao::countLower($userInfo->referee_id);
                        $upgrade_need_num = $system_param['upgrade_need_num'];
                        if ($lowerNum >= $upgrade_need_num) {
                            //上级升级艾天使
                            UserDao::getById($userInfo->referee_id)->update(['level'=>2]);
                        }
                    }
                    //提成金额
                    $subordinate_sales_commission = $system_param['subordinate_sales_commission'];
                    //上级奖励提成
                    if(UserDao::getById($refereeInfo->id)->increment('balance', $subordinate_sales_commission * 100)) {
                        //写入支付记录
                        $payLogData = array(
                            'user_id' => $refereeInfo->id,
                            'openid' => $refereeInfo->openid,
                            'pay_type' => config('statuses.payLog.payType.subordinate.code'),
                            'gain' => $subordinate_sales_commission*100,
                            'expense' => 0,
                            'balance' => $refereeInfo->balance + $subordinate_sales_commission*100,
                            'order_id' => $user_id,
                        );
                        PayLogsService::store($payLogData);
                        //消息提示
                        $template = config('templatemessage.getCommission');
                        $templateData = array(
                            'first' => '您好，您获得了一笔新的佣金。',
                            'keyword1' => sprintf("%.2f", $subordinate_sales_commission) .'元',
                            'keyword2' => date('Y-m-d H:i:s'),
                            'remark' => '请进入系统查看详情！',
                        );
                        $url = config('app.url').'/home/income/0';
                        WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
                        if ($refereeInfo->vip) {
                            $vip_extra_bonus = $system_param['vip_extra_bonus'];
                            if(UserDao::profit($vip_extra_bonus * 100, $refereeInfo->id)) {
                                $payLogData = array(
                                    'user_id' => $refereeInfo->id,
                                    'openid' => $refereeInfo->openid,
                                    'pay_type' => config('statuses.payLog.payType.vip.code'),
                                    'gain' => $vip_extra_bonus*100,
                                    'expense' => 0,
                                    'balance' => $refereeInfo->balance + $vip_extra_bonus*100,
                                    'order_id' => $user_id,
                                );
                                PayLogsService::store($payLogData);
                                //消息提示
                                $templateData = array(
                                    'first' => '您好，您获得了一笔VIP佣金。',
                                    'keyword1' => sprintf("%.2f", $vip_extra_bonus) .'元',
                                    'keyword2' => date('Y-m-d H:i:s'),
                                    'remark' => '请进入系统查看详情！',
                                );
                                WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
                            }
                        }
                    }
                    //上上级奖励提成
                    if ($refereeInfo->referee_id > 0) {
                        $firstInfo = UserDao::findById($refereeInfo->referee_id);
                        //艾天使有下下级提成金额
                        if ($firstInfo->level == 2) {
                            $lowest_sales_commission = $system_param['lowest_sales_commission'];
                            if(UserDao::profit($lowest_sales_commission * 100, $firstInfo->id)) {
                                //写入支付记录
                                $payLogData = array(
                                    'user_id' => $firstInfo->id,
                                    'openid' => $firstInfo->openid,
                                    'pay_type' => config('statuses.payLog.payType.lowest.code'),
                                    'gain' => $lowest_sales_commission*100,
                                    'expense' => 0,
                                    'balance' => $firstInfo->balance + $lowest_sales_commission*100,
                                    'order_id' => $refereeInfo->id,
                                );
                                PayLogsService::store($payLogData);
                                //消息提示
                                $templateData = array(
                                    'first' => '您好，您获得了一笔新的佣金。',
                                    'keyword1' => sprintf("%.2f", $lowest_sales_commission) .'元',
                                    'keyword2' => date('Y-m-d H:i:s'),
                                    'remark' => '请进入系统查看详情！',
                                );
                                $url = config('app.url').'/home/income/0';
                                WechatNoticeService::sendTemplateMessage($firstInfo->id, $firstInfo->openid, $url, $template['template_id'], $templateData);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 推荐店铺奖励
     * @param  [type] $orderInfo [description]
     * @return [type]            [description]
     */
    public static function agentRefereeMoney($orderInfo) {
        //系统参数
        $system_param = SystemDao::getAll();
        //消息模板
        $template = config('templatemessage.getCommission');
        //减库存标记
        $hasUpdateStock = 0;
        //商品数量
        $goodsNum = OrderService::orderGoodsNum($orderInfo->id);
        //用户信息
        $inInfo = UserDao::findById($orderInfo->user_id);
        //是否是店中店
        $inAgent = AgentService::findUserInShop($orderInfo->user_id);
        if (!empty($inAgent)) {
            $agent_referee_id =  $inAgent->referee_id ?? 0;
            $agent_openId = $inAgent->openid ?? 0;
            $referee_type = 'in';
            //扣除库存
            if (! $hasUpdateStock) {
                AgentService::updateStock($inAgent->id, $goodsNum);
                $hasUpdateStock = 1;
            }
            if(UserDao::profit($system_param['sales_inside_shop_profit'] * 100, $orderInfo->user_id)) {
                //写入支付记录
                $payLogData = array(
                    'user_id' => $orderInfo->user_id,
                    'openid' => $orderInfo->openid,
                    'pay_type' => config('statuses.payLog.payType.shopProfit.code'),
                    'gain' => $system_param['sales_inside_shop_profit']*100,
                    'expense' => 0,
                    'balance' => $inInfo->balance + $system_param['sales_inside_shop_profit']*100,
                    'order_id' => $orderInfo->id,
                );
                PayLogsService::store($payLogData);
                //消息提示
                if ($orderInfo->openid) {
                    $templateData = array(
                        'first' => '您好，您获得了一笔新的佣金。',
                        'keyword1' => sprintf("%.2f", $system_param['sales_inside_shop_profit']) .'元',
                        'keyword2' => date('Y-m-d H:i:s'),
                        'remark' => '请进入系统查看详情！',
                    );
                    $url = config('app.url').'/home/income/0';
                    WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $url, $template['template_id'], $templateData);
                }
                //有店中店推荐人，发放推荐人奖励
                if ($agent_referee_id) {
                    $refereeInfo = UserDao::findById($agent_referee_id);
                    if(UserDao::profit($system_param['recommended_inside_shop_sales_commission'] * 100, $agent_referee_id)) {
                        //写入支付记录
                        $payLogData = array(
                            'user_id' => $agent_referee_id,
                            'openid' => $refereeInfo->openid,
                            'pay_type' => config('statuses.payLog.payType.shopReward.code'),
                            'gain' => $system_param['recommended_inside_shop_sales_commission']*100,
                            'expense' => 0,
                            'balance' => $refereeInfo->balance + $system_param['recommended_inside_shop_sales_commission']*100,
                            'order_id' => $orderInfo->id,
                        );
                        PayLogsService::store($payLogData);
                        //消息提示
                        $templateData = array(
                            'first' => '您好，您获得了一笔新的佣金。',
                            'keyword1' => sprintf("%.2f", $system_param['recommended_inside_shop_sales_commission']) .'元',
                            'keyword2' => date('Y-m-d H:i:s'),
                            'remark' => '请进入系统查看详情！',
                        );
                        $url = config('app.url').'/home/income/0';
                        WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
                    }
                }
            }
        }
        //查找提货店(区域)
        $areaAgent = AgentService::findAgentByAddress($orderInfo->province, $orderInfo->city, $orderInfo->area);
        if (!empty($areaAgent)) {
            $agent_referee_id =  $areaAgent->referee_id ?? 0;
            $agent_openId = $areaAgent->openid ?? 0;
            $referee_type = 'area';
            //有区域店，发放区域店提成15
            $areaInfo = UserDao::findById($areaAgent->user_id);
            if(UserDao::profit($system_param['sales_area_shop_profit'] * 100, $areaAgent->user_id)) {
                //写入支付记录
                $payLogData = array(
                    'user_id' => $areaAgent->user_id,
                    'openid' => $agent_openId,
                    'pay_type' => config('statuses.payLog.payType.shopProfit.code'),
                    'gain' => $system_param['sales_area_shop_profit']*100,
                    'expense' => 0,
                    'balance' => $areaInfo->balance + $system_param['sales_area_shop_profit']*100,
                    'order_id' => $orderInfo->id,
                );
                PayLogsService::store($payLogData);
                //扣除库存
                if (! $hasUpdateStock) {
                    AgentService::updateStock($areaAgent->id, OrderService::orderGoodsNum($orderInfo->id));
                    $hasUpdateStock = 1;
                }
                //消息提示
                if ($agent_openId) {
                    $templateData = array(
                        'first' => '您好，您获得了一笔新的佣金。',
                        'keyword1' => sprintf("%.2f", $system_param['sales_area_shop_profit']) .'元',
                        'keyword2' => date('Y-m-d H:i:s'),
                        'remark' => '请进入系统查看详情！',
                    );
                    $url = config('app.url').'/home/income/0';
                    WechatNoticeService::sendTemplateMessage($areaAgent->user_id, $agent_openId, $url, $template['template_id'], $templateData);
                }
                //有区域店推荐人，发放推荐人奖励
                if ($agent_referee_id) {
                    $refereeInfo = UserDao::findById($agent_referee_id);
                    if(UserDao::profit($system_param['recommended_area_shop_sales_commission'] * 100, $agent_referee_id)) {
                        //写入支付记录
                        $payLogData = array(
                            'user_id' => $agent_referee_id,
                            'openid' => $refereeInfo->openid,
                            'pay_type' => config('statuses.payLog.payType.shopReward.code'),
                            'gain' => $system_param['recommended_area_shop_sales_commission']*100,
                            'expense' => 0,
                            'balance' => $refereeInfo->balance + $system_param['recommended_area_shop_sales_commission']*100,
                            'order_id' => $orderInfo->id,
                        );
                        PayLogsService::store($payLogData);
                        //消息提示
                        $templateData = array(
                            'first' => '您好，您获得了一笔新的佣金。',
                            'keyword1' => sprintf("%.2f", $system_param['recommended_area_shop_sales_commission']) .'元',
                            'keyword2' => date('Y-m-d H:i:s'),
                            'remark' => '请进入系统查看详情！',
                        );
                        $url = config('app.url').'/home/income/0';
                        WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
                    }
                }
            }
        }
        //是否有旗舰店
        $cityAgent = AgentService::findAgentByAddress($orderInfo->province, $orderInfo->city);
        if (!empty($cityAgent)) {
            $agent_referee_id =  $cityAgent->referee_id ?? 0;
            $agent_openId = $cityAgent->openid ?? 0;
            $referee_type = 'city';
            //有旗舰店，发放旗舰店提成20
            $cityInfo = UserDao::findById($cityAgent->user_id);
            if(UserDao::profit($system_param['sales_city_shop_profit'] * 100, $cityAgent->user_id)) {
                //写入支付记录
                $payLogData = array(
                    'user_id' => $cityAgent->user_id,
                    'openid' => $agent_openId,
                    'pay_type' => config('statuses.payLog.payType.shopProfit.code'),
                    'gain' => $system_param['sales_city_shop_profit']*100,
                    'expense' => 0,
                    'balance' => $cityInfo->balance + $system_param['sales_city_shop_profit']*100,
                    'order_id' => $orderInfo->id,
                );
                PayLogsService::store($payLogData);
                //扣除库存
                if (! $hasUpdateStock) {
                    AgentService::updateStock($cityAgent->id, OrderService::orderGoodsNum($orderInfo->id));
                    $hasUpdateStock = 1;
                }
                //消息提示
                if ($agent_openId) {
                    $templateData = array(
                        'first' => '您好，您获得了一笔新的佣金。',
                        'keyword1' => sprintf("%.2f", $system_param['sales_city_shop_profit']) .'元',
                        'keyword2' => date('Y-m-d H:i:s'),
                        'remark' => '请进入系统查看详情！',
                    );
                    $url = config('app.url').'/home/income/0';
                    WechatNoticeService::sendTemplateMessage($cityAgent->user_id, $agent_openId, $url, $template['template_id'], $templateData);
                }

                //有旗舰店推荐人，发放推荐人奖励
                if ($agent_referee_id) {
                    $refereeInfo = UserDao::findById($agent_referee_id);
                    if(UserDao::profit($system_param['recommended_city_shop_sales_commission'] * 100, $agent_referee_id)) {
                        //写入支付记录
                        $payLogData = array(
                            'user_id' => $agent_referee_id,
                            'openid' => $refereeInfo->openid,
                            'pay_type' => config('statuses.payLog.payType.shopReward.code'),
                            'gain' => $system_param['recommended_city_shop_sales_commission']*100,
                            'expense' => 0,
                            'balance' => $refereeInfo->balance + $system_param['recommended_city_shop_sales_commission']*100,
                            'order_id' => $orderInfo->id,
                        );
                        PayLogsService::store($payLogData);
                        //消息提示
                        $templateData = array(
                            'first' => '您好，您获得了一笔新的佣金。',
                            'keyword1' => sprintf("%.2f", $system_param['recommended_city_shop_sales_commission']) .'元',
                            'keyword2' => date('Y-m-d H:i:s'),
                            'remark' => '请进入系统查看详情！',
                        );
                        $url = config('app.url').'/home/income/0';
                        WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
                    }
                }
            }
        }
        //无推荐人推荐收益归店
        if ($inInfo->referee_id == 0) {
            $tuiUser = NULL; //计入店的用户
            if (!empty($inAgent)) {
                $tuiUser = $inInfo;
            } elseif (!empty($areaAgent)) {
                $tuiUser = $areaInfo;
            } elseif (!empty($cityAgent)) {
                $tuiUser = $cityInfo;
            }
            if (!empty($tuiUser)) {
                if(UserDao::profit($system_param['subordinate_sales_commission'] * 100, $tuiUser->id)) {
                    $template = config('templatemessage.getCommission');
                    //写入支付记录
                    $payLogData = array(
                        'user_id' => $tuiUser->id,
                        'openid' => $tuiUser->openid,
                        'pay_type' => config('statuses.payLog.payType.shopProfit.code'),
                        'gain' => $system_param['subordinate_sales_commission']*100,
                        'expense' => 0,
                        'balance' => $tuiUser->balance + $system_param['subordinate_sales_commission']*100,
                        'order_id' => $orderInfo->id,
                    );
                    PayLogsService::store($payLogData);
                    //消息提示
                    if ($tuiUser->openid) {
                        $templateData = array(
                            'first' => '您好，您获得了一笔新的佣金。',
                            'keyword1' => sprintf("%.2f", $system_param['subordinate_sales_commission']) .'元',
                            'keyword2' => date('Y-m-d H:i:s'),
                            'remark' => '请进入系统查看详情！',
                        );
                        $url = config('app.url').'/home/income/0';
                        WechatNoticeService::sendTemplateMessage($tuiUser->id, $tuiUser->openid, $url, $template['template_id'], $templateData);
                    }
                }
            }
        }
    }

    public static function buildShopRecommend($agent, $reward) {
        //消息模板
        $template = config('templatemessage.getCommission');
        if(UserDao::getById($agent->referee_id)->increment('balance', $reward * 100)) {
            $userInfo = UserDao::findById($agent->referee_id);
            //日志
            $payLogData = array(
                'user_id' => $agent->referee_id,
                'openid' => $userInfo->openid,
                'pay_type' => config('statuses.payLog.payType.recommendShop.code'),
                'gain' => $reward*100,
                'expense' => 0,
                'balance' => $userInfo->balance,
                'order_id' => $agent->id,
            );
            PayLogsService::store($payLogData);
            //消息提示
            $templateData = array(
                'first' => '您好，您获得了一笔新的佣金。',
                'keyword1' => sprintf("%.2f", $reward) .'元',
                'keyword2' => date('Y-m-d H:i:s'),
                'remark' => '请进入系统查看详情！',
            );
            $url = config('app.url').'/home/income/0';
            WechatNoticeService::sendTemplateMessage($agent->user_id, $userInfo->openid, $url, $template['template_id'], $templateData);
        }
    }
    
    public static function balancePay($payment, $user_id) {
        return UserDao::getById($user_id)->decrement('balance', $payment);
    }

    //锁定余额
    public static function lockBalance($amount, $user_id) {
        return UserDao::lockBalance($amount, $user_id);
    }
    /**
     * 查询推荐人级别
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public static function findRefereeLevel($user_id) {
        return UserDao::findRefereeLevel($user_id);
    }

    /**
     * 根据openid查询用户
     * @param  string $openid
     *
     * @return App\Models\Users\User
     */
    public static function findByOpenid($openid) {
        return UserDao::findByOpenid($openid);
    }

    /**
     * 根据phone查询用户
     * @param  string $phone
     *
     * @return App\Models\Users\User
     */
    public static function findByPhone($phone) {
        return UserDao::findByPhone($phone);
    }

    public static function addUser($data) {
        return UserDao::addUser($data);
    }

    /**
     * 保存更新user
     * @param array $request
     * @param number $id
     */
    public static function saveOrUpdate($openid, $request) {
        return UserDao::saveOrUpdate($openid, $request);
    }

    /**
     * 根据Id查询用户
     * @param int $id
     *
     */
    public static function findById($id) {
        return UserDao::findById($id);
    }

    /**
     * 获取用户
     * @param int $id
     *
     */
    public static function getById($id) {
        return UserDao::getById($id);
    }

    /**
     * 判断某个字段是否已经存在某个值
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($key, $name, $id = 0) {
        $result = UserDao::existColumn($key, $name, $id);
        if ($result) {
            return array(
                'code' => 500,
                'messages' => array('昵称已存在'),
                'url' => '',
            );
        }
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params = array()) {
        return UserDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 保存
     * @param  App\Models\Users\User $user
     *
     * @return array
     */
    public static function update($user) {
        $user = UserDao::save($user, null);
        if (!$user) {
            return array(
                'code' => 500,
                'messages' => array('更新用户失败'),
                'url' => '',
            );
        }

        return array(
            'code' => 200,
            'messages' => array('更新用户成功'),
            'url' => '',
        );
    }
    
    /**
     * 获取团队数据
     * @param unknown $type
     */
    public static function getTeam($type)
    {
        return UserDao::getTeam($type);
    }
}
