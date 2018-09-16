<?php

namespace App\Services;

use App\Daoes\SmsLogDao;
use Illuminate\Support\Facades\Log;

class SmsLogService
{
    const TARGET = 'http://sms.chanzor.com:8001/sms.aspx';
    const OVERAGE = 'http://api.chanzor.com/overage';
    const SEND_URL = 'http://api.chanzor.com/send';
    const STATUS_REPORT = 'http://api.chanzor.com/dr';
    const SINGLE_SENDING_LIMIT = 100;
    const TIME_LIMITED = 12;
    const WAIT_TIME = 2;
    const SMS_SUCCESS_STATUS = 10;
    const SMS_FAIL_STATUS = 20;
    const SMS_MAX_LENGTH = 800;
    const SMS_SINGLE_LENGTH = 67;
    const SMS_DEFAULT_LENGTH = 70;
    const DEFAULT_SIGN = '【52gai】';
    
    
    public static function sendPhoneCode($phone) {
        $smsCode   = rand('1000', '9999');
        $smsResult = self::sendVerifyCode(array($phone), $smsCode);
        $smsType   = config('smsLog.type.verifyCode.code');
        SmsLogDao::saveSms(array($phone), $smsCode, $smsType, $smsResult, null, request('ip', getRealIp()));
        if ($smsResult) {
            return array(
                'code'          => 200,
                'messages'      => array('验证码已发送成功'),
                'redirectUrl'   => '',
                'effectiveTime' => 300
            );
        } else {
            return array(
                'code'          => 500,
                'messages'      => array(json_encode($smsResult).'发送手机校验码失败!'),
                'redirectUrl'   => '',
                'effectiveTime' => 0
            );
        }
    }
    
    /**
     * Send the verify code to the mobiles
     *
     * @param  $mobiles
     * @param  $verifyCode
     *
     * @return  true if success
     */
    public static function sendVerifyCode($mobiles = array(), $verifyCode = '') {
        $content = '您的短信验证码：' . $verifyCode . '，请勿向他人提供您收到的验证码。如非本人操作，请忽略此短信【52改】';
        
        return self::send($mobiles, $content);
    }
    
    /**
     * Send the password to the mobiles
     *
     * @param  $mobiles
     * @param  $password
     *
     * @return  true if success
     */
    public static function sendPassword($mobiles = array(), $password = '', $shopNumber = '', $userName = '') {
        $content = '您已可以登录系统，';
        if ($shopNumber != '') {
            $content = $content . '门店编号：' . $shopNumber;
        }
        
        if ($userName != '') {
            $content = $content . '，账号:' . $userName;
        }
        
        $content = $content . '，登录密码：' . $password
        . '，请勿向任何人提供您的密码。登录系统后请修改密码。【车网联盟】';
        
        return self::send($mobiles, $content);
    }
    
    /**
     * reset the shop's password to the mobiles
     *
     * @param  $mobiles
     * @param  $password
     *
     * @return  true if success
     */
    public static function sendShopPassword($mobiles, $password, $shopNumber, $bossName) {
        $content = '您已可以登录ERP系统。门店编号:' . $shopNumber . '； 用户名:' . $bossName . ' ；登录密码:' . $password .
        '。您可登录后重置密码。请勿向任何人提供您的密码。【车网联盟】';
        return self::send($mobiles, $content);
    }
    
    /**
     * reset the password to the mobiles
     *
     * @param  $mobiles
     * @param  $password
     *
     * @return  true if success
     */
    public static function sendResetPassword($mobiles, $password, $shopNumber, $bossName) {
        $content = '您的ERP登录密码已被重置，请使用新密码登录。门店编号:' . $shopNumber . ' 用户名:' . $bossName . ' 登录密码:' . $password .
        '。请勿向任何人提供您的密码。【车网联盟】';
        return self::send($mobiles, $content);
    }
    
    /**
     * reset the password to the mobiles
     *
     * @param  $mobiles
     * @param  $password
     *
     * @return  true if success
     */
    public static function sendRegistrationPassAudit($mobiles) {
        $content = '您提交的注册ERP系统审核已经通过，
        您可以在系统登录页面的“账号注册->查询进度”流程中，通过该手机号码查询审核进度，进行下一步操作。
        或点击 t.cn/RYTqeKk 查询
        【车网联盟】';
        return self::send($mobiles, $content);
    }
    
    /**
     * reset the password to the mobiles
     *
     * @param  $mobiles
     * @param  $reason
     *
     * @return  true if success
     */
    public static function sendRegistrationNoPassAudit($mobiles, $reason) {
        $content = '您提交注册车网联盟ERP系统审核不通过，
        不通过原因：' . $reason . '
        可重新注册提交。
        【车网联盟】';
        return self::send($mobiles, $content);
    }
    
    /**
     * Send the verify code to the mobiles
     *
     * @param  $mobiles
     * @param  $verifyCode
     *
     * @return  true if success
     */
    public static function expiredReminder($mobiles = array(), $reason = '') {
        return self::send($mobiles, $reason);
    }
    
    /**
     * Send the content to the mobiles.
     *
     * @param  $mobiles
     * @param  $content
     *
     * @return bool, true if success
     */
    private static function send($mobiles, $content) {
        $account = env('SMS_ACCOUNT');
        $password = env('SMS_PASSWORD');
        
        if (is_array($mobiles)) {
            $mobiles = implode(',', $mobiles);
        }
        
        $post_data = 'action=send&userid=&account=' . $account
        . '&password=' . $password
        . '&mobile=' . $mobiles
        . '&sendTime=&content=' . rawurlencode($content);
        
        $response = self::smsPost($post_data, self::TARGET);
        
        $start = strpos($response, '<?xml');
        $data = substr($response, $start);
        
        $result = false;
        $xml = simplexml_load_string($data);
        
        if (!$xml) {
            log::error('send sms failed. mobiles = ' . $mobiles . ', content = ' . $content);
            
        }
        if (isset($xml->returnstatus) && strtolower($xml->returnstatus) === 'success') {
            log::info('send sms successfully. mobiles = ' . $mobiles . ', content = ' . $content);
            $result = true;
        } else {
            log::error('send sms failed. data ' . $data);
        }
        
        return $result;
    }
    
    /**
     * The demo code from chanzor to post the request to it's server.
     *
     * @param  $data the data to be sent.
     * @param  $target the usl of the server.
     *
     * @return  response from the server.
     */
    private static function smsPost($data, $target) {
        $url_info = parse_url($target);
        
        $httpheader = 'POST ' . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= 'Host:' . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= 'Content-Length:' . strlen($data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        $httpheader .= $data;
        
        $fd = fsockopen($url_info['host'], 80);
        fwrite($fd, $httpheader);
        $response = '';
        while (!feof($fd)) {
            $response .= fread($fd, 128);
        }
        fclose($fd);
        
        return $response;
    }
    
    /**
     * New Send connector.
     *
     * @param  array $mobiles
     * @param  string $content
     *
     * @return array
     */
    public static function smsSend($mobiles, $content) {
        $results = array();
        
        $account = env('SMS_ACCOUNT_YX');
        $password = env('SMS_PASSWORD_YX');
        
        $password = mb_strtoupper(md5($password));
        
        $sendTimes = ceil(count($mobiles) / self::SINGLE_SENDING_LIMIT);
        for ($i = 0; $i < $sendTimes; $i++) {
            $partMobiles = array_slice($mobiles, $i * 100, 100);
            $receiverMobiles = implode(',', $partMobiles);
            
            try {
                $params = array(
                        'account' => $account,
                        'password' => $password,
                        'mobile' => $receiverMobiles,
                        'content' => $content,
                );
                
                $client = new Client();
                $response = $client->request(
                        'GET',
                        self::SEND_URL,
                        array('query' => $params)
                        );
                
                $result = $response->getBody()->getContents();
                Log::info('短信发送结果=' . $result);
                
                $results[] = $result;
            } catch (Exception $e) {
                Log::error('Exception $e = ' . json_encode($e));
                $results[] = '';
            }
        }
        
        return $results;
    }
    
    /**
     * Format data.
     *
     * @param  array $data
     *
     * @return array
     */
    private static function formatStatusReport($sendResult) {
        $successMobiles = array();
        $failMobiles = array();
        $taskIds = array();
        
        $result = json_decode($sendResult);
        if (!isset($result->status) || $result->status != 0) {
            return array();
        }
        
        if (!isset($result->taskId)) {
            return array();
        }
        
        $timeLimit = self::TIME_LIMITED;
        while ($timeLimit > 0) {
            $statusReport =  self::smsStatusReport($result->taskId);
            if (count($statusReport->drList) == 0) {
                sleep(self::WAIT_TIME);
                $timeLimit -= self::WAIT_TIME;
            } else {
                $timeLimit = 0;
            }
            
            if (count($statusReport->drList) > 0) {
                foreach ($statusReport->drList as $record) {
                    if ($record->status == self::SMS_SUCCESS_STATUS) {
                        $successMobiles[] = $record->mobile;
                    } elseif ($record->status == self::SMS_FAIL_STATUS) {
                        $failMobiles[] = $record->mobile;
                    }
                    
                    $taskIds[$record->mobile] = $result->taskId;
                }
            }
        }
        
        return array(
                'successMobiles' => $successMobiles,
                'failMobiles' => $failMobiles,
                'taskIds' => $taskIds,
        );
    }
    
    /**
     * SMS number remaining.
     *
     * @return int
     */
    public static function smsOverage() {
        $account = env('SMS_ACCOUNT_YX');
        $password = env('SMS_PASSWORD_YX');
        
        try {
            $client = new Client();
            $response = $client->request(
                    'GET',
                    self::OVERAGE,
                    array('query' => array(
                            'account' => $account,
                            'password' => mb_strtoupper(md5($password)),
                    ))
                    );
            $data = json_decode($response->getBody()->getContents());
            if (!isset($data->status) || $data->status != 0) {
                return 0;
            }
            
            if (!isset($data->overage)) {
                return 0;
            }
        } catch (Exception $re) {
            return 0;
        }
        
        return $data->overage;
    }
    
    /**
     * SMS status report.
     *
     * @param  int $taskId
     *
     * @return array
     */
    public static function smsStatusReport($taskId) {
        $account = env('SMS_ACCOUNT_YX');
        $password = env('SMS_PASSWORD_YX');
        
        $url = self::STATUS_REPORT . '?account=' . $account
        . '&password=' . mb_strtoupper(md5($password))
        . '&taskId=' . $taskId
        . '&statusNum=' . self::SINGLE_SENDING_LIMIT;
        $content = file_get_contents($url);
        
        $data = json_decode($content);
        if (!isset($data->status) || $data->status != 0) {
            Log::error('获取短信状态报告数据失败，taskId=' . $taskId);
            
            return array();
        }
        
        if (!isset($data->drList)) {
            return array();
        }
        
        return $data;
    }
    
    /**
     * Validate sms content.
     *
     * @param  string $smsContent
     *
     * @return array
     */
    public static function validateContent($content) {
        $smsLength = mb_strlen($content);
        
        if ($smsLength > self::SMS_MAX_LENGTH) {
            return array(
                    'code' => 1,
                    'messages' => array('短信内容最大为800字。'),
            );
        }
        
        return array(
                'code' => 0,
                'messages' => array('校验完成'),
        );
    }
    
    /**
     * Number of text messages.
     *
     * @param  string $content
     *
     * @return int $smsNum
     */
    public static function costSmsCount($content) {
        $smsNum = 1;
        
        $smsLength = mb_strlen($content);
        if ($smsLength > self::SMS_DEFAULT_LENGTH) {
            $smsNum = ceil($smsLength / self::SMS_SINGLE_LENGTH);
        }
        
        return $smsNum;
    }
}
