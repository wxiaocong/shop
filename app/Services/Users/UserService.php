<?php

namespace App\Services\Users;

use App\Daoes\Users\UserDao;
use App\Daoes\Admins\SystemDao;
use App\Services\AgentService;
use App\Services\PayLogsService;
use App\Services\WechatNoticeService;

class UserService {
    /**
     * 付款用户及上级级别变更
     * 奖励分配
     */
    public static function upgradeUserLevel($user_id) {
        $userInfo = UserDao::findById($user_id);
        if (!empty($userInfo)) {
            //游客下单付款升级艾达人
            if ($userInfo->level == 0) {
               UserDao::getById($user_id)->update(['level'=>1]);
            }
            //上级艾达人如果有5个下级艾达人，升级艾天使
            if ($userInfo->referee_id > 0) {
                //系统参数
                $system_param = SystemDao::getAll();
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
                    $vip_extra_bonus = 0;
                    if ($refereeInfo->vip) {
                        $vip_extra_bonus = $system_param['vip_extra_bonus'];
                    }
                    //上级奖励提成
                    if(UserDao::getById($refereeInfo->id)->increment('balance', ($subordinate_sales_commission+$vip_extra_bonus) * 100)) {
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
                    //上上级奖励提成
                    if ($refereeInfo->referee_id > 0) {
                        $firstInfo = UserDao::findById($refereeInfo->referee_id);
                        //提成金额
                        $lowest_sales_commission = $system_param['lowest_sales_commission'];
                        if(UserDao::getById($firstInfo->id)->increment('balance', $lowest_sales_commission * 100)) {
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

    /**
     * 推荐店铺奖励
     * @param  [type] $orderInfo [description]
     * @return [type]            [description]
     */
    public static function agentRefereeMoney($orderInfo) {
        //查找提货店(区域)
        $areaAgent = AgentService::findAgentByAddress($orderInfo->province, $orderInfo->city, $orderInfo->area);
        $agent_referee_id =  $areaAgent->referee_id ?? 0;
        $agent_openId = $areaAgent->openid ?? 0;
        $referee_type = 'area';
        if ($agent_referee_id == 0) {
            //没有区域店，查找旗舰店
            $cityAgent = AgentService::findAgentByAddress($orderInfo->province, $orderInfo->city);
            $agent_referee_id =  $cityAgent->referee_id ?? 0;
            $agent_openId = $cityAgent->openid ?? 0;
            $referee_type = 'city';
        }
        if ($agent_referee_id > 0) {
            //系统参数
            $system_param = SystemDao::getAll();
            if ($referee_type == 'area') {
                $rewardMoney = $system_param['recommended_area_shop_sales_commission'];
            } else {
                $rewardMoney = $system_param['recommended_flagship_shop_sales_commission'];
            }
            $refereeInfo = UserDao::findById($agent_referee_id);
            if(UserDao::getById($agent_referee_id)->increment('balance', $rewardMoney * 100)) {
                //写入支付记录
                $payLogData = array(
                    'user_id' => $agent_referee_id,
                    'openid' => $agent_openId,
                    'pay_type' => config('statuses.payLog.payType.shopReward.code'),
                    'gain' => $rewardMoney*100,
                    'expense' => 0,
                    'balance' => $refereeInfo->balance + $rewardMoney*100,
                    'order_id' => $orderInfo->id,
                );
                PayLogsService::store($payLogData);
                //消息提示
                $templateData = array(
                    'first' => '您好，您获得了一笔新的佣金。',
                    'keyword1' => sprintf("%.2f", $rewardMoney) .'元',
                    'keyword2' => date('Y-m-d H:i:s'),
                    'remark' => '请进入系统查看详情！',
                );
                WechatNoticeService::sendTemplateMessage($refereeInfo->id, $refereeInfo->openid, $url, $template['template_id'], $templateData);
            }
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
