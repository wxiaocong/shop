<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\WechatNotice;

class WechatNoticeDao extends BaseDao
{
    public static function findById($noticeId)
    {
        return WechatNotice::find($noticeId);
    }
}
