<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\SmsLog;

class SmsLogDao extends BaseDao
{
    /**
     * store SMS info.
     *
     * @param  string $mobile
     *
     * @return int
     */
    public static function saveSms($mobiles, $content, $type, $smsResult, $user, $ip = '0.0.0.0')
    {
        $status = config('smsLog.status.fail.code');
        if ($smsResult) {
            $status = config('smsLog.status.success.code');
        }
        
        if (!is_array($mobiles)) {
            $mobiles = array($mobiles);
        }
        
        $smsLogs = array();
        foreach ($mobiles as $mobile) {
            $smsLog          = new SmsLog();
            $smsLog->created_at = date('Y-m-d H:i:s');
            $smsLog->ip      = $ip;
            $smsLog->mobile  = $mobile;
            $smsLog->content = $content;
            $smsLog->type    = $type;
            $smsLog->status  = $status;
            if ($user != null) {
                $smsLog->user_id = $user->id;
            }
            $smsLogs[] = $smsLog->toArray();
        }
        SmsLog::insert($smsLogs);
        return true;
    }
    
    /**
     * 更新验证码为已使用
     * @param int $phone
     * @param int $phone_code
     * @return int
     */
    public static function userdCode($phone,$phone_code)
    {
        return SmsLog::where(['mobile'=>$phone,'content'=>$phone_code, 'status'=>0])->increment('status',1);
    }
    
    //查询最新验证码
    public static function findByMobile($mobiles, $ip = '0.0.0.0')
    {
        $result = SmsLog::where('ip', $ip)->where(['mobile'=>$mobiles,'status'=>0])->orderBy('id', 'desc')->first();
        if ($result) {
            //检查验证码是否过期
            if (strtotime($result->created_at) + config('smsLog.validity') < strtotime(date('Y-m-d H:i:s'))) {
                $result = NULL;
            }
        }
        return $result;
    }
}
