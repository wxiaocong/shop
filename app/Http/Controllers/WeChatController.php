<?php

namespace App\Http\Controllers;

use App\Services\GoodsSpecService;
use App\Services\OrderRefundService;
use App\Services\OrderService;
use App\Services\AgentService;
use App\Services\PayLogsService;
use App\Services\Users\UserService;
use App\Services\WechatNoticeService;
use App\Services\WechatNotifyService;
use EasyWeChat;
use Illuminate\Support\Facades\DB;
use Session;

class WeChatController extends Controller {
    //网页授权
    public function oauthCallback()
    {
        $app = EasyWeChat::officialAccount();
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user()->toArray();
        $openid = $user['id'];
        //获取用户头像等信息
        $user = $app->user->get($openid);
        if (!empty($user)) {
            //保存微信通知数据
            WechatNotifyService::store($user);
            $userInfo = UserService::findByOpenid($openid);
            $wechatUserData = array(
                'subscribe' => $user['subscribe'],
                'subscribe_time' => $user['subscribe_time'],
                'nickname' => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
                'city' => $user['city'],
                'province' => $user['province'],
                'country' => $user['country'],
                'sex' => $user['sex'],
            );
            if(empty($userInfo)) {
                if (env('APP_SYSTEM_TYPE') == 'test') {
                    $wechatUserData['balance'] = 5000000;
                }
                if (session('target_url')) {
                    preg_match('/shareId=(\d+)/', session('target_url'), $match);
                    if (!empty($match)) {
                        if(UserService::getById($match['1'])->exists()) {
                            $wechatUserData['referee_id'] = $match['1'];
                        }
                    }
                }
            }
            UserService::saveOrUpdate($openid, $wechatUserData);
            Session::forget('user');
            session(array('user' => UserService::findByOpenid($openid)));
        }
        $targetUrl = empty(session('target_url')) ? config('app.url') : session('target_url');
        session::forget('target_url');
        return redirect($targetUrl);
    }

    //微信支付通知
    public function payNotice()
    {
        $app = EasyWeChat::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($message);
            //微信查询订单状态
            $searchApp = EasyWeChat::payment();
            $result = $searchApp->order->queryByOutTradeNumber($message['out_trade_no']);
            if ($result['return_code'] === 'SUCCESS') {
                if ($result['trade_state'] === 'SUCCESS') {
                    //查询订单
                    $orderSn = substr($message['out_trade_no'], 0, 22);
                    $orderInfo = OrderService::findAddGoodByOrderSn($orderSn);
                    //未找到订单或订单不是未付款状态，退款
                    if (empty($orderInfo) || $orderInfo->state != 1) {
//                         OrderRefundService::wechatRefund($message['out_trade_no'], $message['out_trade_no'] . time(), $message['total_fee'], $result['cash_fee']);
                        return true;
                    }
                    $pay_time = date('Y-m-d H:i:s', strtotime($result['time_end']));
                    $updateData = array(
                        'real_pay' => $result['cash_fee'], //实付款
                        'pay_time' => $pay_time, //付款时间
                        'transaction_id' => $result['transaction_id'], //微信支付订单号
                        'state' => 2, //已付款
                    );
                    $res = OrderService::noticeUpdateOrder($orderInfo->id, $updateData);
                    //开始事务
                    DB::beginTransaction();
                    try {
                        //更新订单状态
                        if ($res) {
                            //更新库存
                            // GoodsSpecService::updateGoodsSpecNum($orderInfo->id);
                            //用户级别变更及销售奖励分配
                            UserService::upgradeUserLevel($orderInfo->user_id);
                            //推荐店铺奖励
                            UserService::agentRefereeMoney($orderInfo);
                            //写入支付记录
                            $payLogData = array(
                                'user_id' => $orderInfo->user_id,
                                'openid' => $orderInfo->openid,
                                'pay_type' => 1,
                                'gain' => $result['cash_fee'],
                                'expense' => $result['cash_fee'],
                                'balance' => $orderInfo->balance,
                                'order_id' => $orderInfo->id,
                            );
                            PayLogsService::store($payLogData);

                            DB::commit();
                            //微信通知
                            if ($orderInfo->openid) {
                                $template = config('templatemessage.orderPaySuccess');
                                $templateData = array(
                                    'first' => '您好，您的订单已支付成功',
                                    'keyword1' => '￥' . $result['cash_fee'] / 100,
                                    'keyword2' => $orderInfo->order_sn,
                                    'remark' => '如有问题请联系客服,欢迎再次光临！',
                                );
                                $url = config('app.url').'/order/detail/'.$orderSn;
                                WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $url, $template['template_id'], $templateData);
                            }
                            return true;
                        } else {
                            DB::rollback();
                            return $fail('更新失败');
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $fail('更新失败');
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            return true;
        });
        return $response;
    }

    //申请代理商支付通知
    public function agentNotice()
    {
        $app = EasyWeChat::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($message);
            //微信查询订单状态
            $searchApp = EasyWeChat::payment();
            $result = $searchApp->order->queryByOutTradeNumber($message['out_trade_no']);
            if ($result['return_code'] === 'SUCCESS') {
                if ($result['trade_state'] === 'SUCCESS') {
                    //查询订单
                    $orderSn = $message['out_trade_no'];
                    $orderInfo = AgentService::findOrderSnBalance($orderSn);
                    //未找到订单或订单不是未付款状态
                    if ( empty($orderInfo) || $orderInfo->state != 1) {
                        return true;
                    }
                    $pay_time = date('Y-m-d H:i:s', strtotime($result['time_end']));
                    $updateData = array(
                        'real_pay' => $result['cash_fee'], //实付款
                        'pay_time' => $pay_time, //付款时间
                        'transaction_id' => $result['transaction_id'], //微信支付订单号
                        'state' => 2, //已付款
                    );
                    //推荐人是否为艾天使
                    $refereeLevel = UserService::findRefereeLevel($orderInfo->user_id);
                    if (! empty($refereeLevel) && $refereeLevel['level'] == 2) {
                        $updateData['referee_id'] = $refereeLevel['referee_id'];
                    }
                    //开始事务
                    DB::beginTransaction();
                    try {
                        //更新订单状态
                        if (AgentService::noticeUpdateAgent($orderInfo->id, $updateData)) {
                            //写入支付记录
                            $payLogData = array(
                                'user_id' => $orderInfo->user_id,
                                'openid' => $orderInfo->openid,
                                'pay_type' => 11,
                                'gain' => $result['cash_fee'],
                                'expense' => $result['cash_fee'],
                                'balance' => $orderInfo->balance,
                                'order_id' => $orderInfo->id,
                            );
                            PayLogsService::store($payLogData);

                            DB::commit();
                            //微信通知
                            if ($orderInfo->openid) {
                                $template = config('templatemessage.orderPaySuccess');
                                $templateData = array(
                                    'first' => '您好，您的订单已支付成功',
                                    'keyword1' => '￥' . $result['cash_fee'] / 100,
                                    'keyword2' => $orderInfo->order_sn,
                                    'remark' => '如有问题请联系客服,欢迎再次光临！',
                                );
                                $url = config('app.url').'/agent/detail/'.$orderSn;
                                WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $url, $template['template_id'], $templateData);
                            }
                            return true;
                        } else {
                            DB::rollback();
                            return $fail('更新失败');
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $fail('更新失败');
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            return true;
        });
        return $response;
    }

    /**
     * 模板消息通知
     */
    public function templateMessageNotice()
    {
        $noticeId = intval(request('noticeId', 0));
        if ($noticeId) {
            return WechatNoticeService::findById($noticeId)->update(array('is_received' => 1));
        }
    }

    /**
     * 退款通知
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refundNotice()
    {
        $app = EasyWeChat::payment();
        $response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($reqInfo);
            // 其中 $message['req_info'] 获取到的是加密信息
            // $reqInfo 为 message['req_info'] 解密后的信息
            if ($message['return_code'] === 'SUCCESS' && $reqInfo['refund_status'] === 'SUCCESS') {
                //更新退款
                return OrderRefundService::noticeUpdate($reqInfo);
            }

            //退款失败
            OrderRefundService::refundFailure($reqInfo);
            return $fail('退款失败');
        });
        return $response;
    }

    /**
     * 消息回复
     * @return [type] [description]
     */
    public function response()
    {
        $app = EasyWeChat::officialAccount();
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    if ($message['Event'] == 'subscribe') {
                        //保存微信通知数据
                        WechatNotifyService::store($message);
                        //扫码事件, 创建用户建立上下级
                        if (isset($message['EventKey'])) {
                            $parentId = str_replace('qrscene_','',$message['EventKey']);
                            $openid = $message['FromUserName'];
                            //查询用户是否存在
                            $userInfo = UserService::findByOpenid($openid);
                            if(empty($userInfo)){
                                $app = EasyWeChat::officialAccount();
                                $user = $app->user->get($openid);
                                $wechatUserData = array(
                                    'referee_id' => $parentId,
                                    'subscribe' => $user['subscribe'],
                                    'subscribe_time' => $user['subscribe_time'],
                                    'nickname' => $user['nickname'],
                                    'headimgurl' => $user['headimgurl'],
                                    'city' => $user['city'],
                                    'province' => $user['province'],
                                    'country' => $user['country'],
                                    'sex' => $user['sex'],
                                );
                                if (env('APP_SYSTEM_TYPE') == 'test') {
                                    $wechatUserData['balance'] = 5000000;
                                }
                                UserService::saveOrUpdate($openid, $wechatUserData);
                            }
                        }
                        return '欢迎来到植得艾';
                    }
                    return '';
                    break;
                case 'text':
                    return '';
                    break;
                case 'image':
                    return '';
                    break;
                case 'voice':
                    return '';
                    break;
                case 'video':
                    return '';
                    break;
                case 'location':
                    return '';
                    break;
                case 'link':
                    return '';
                    break;
                case 'file':
                    return '';
                default:
                    return '';
                    break;
            }
        });
        return $app->server->serve();
    }


    /**
     * 获取永久素材列表
     * @return [type] [description]
     */
    public function getMaterialList()
    {
        $app = EasyWeChat::officialAccount();
        $res = $app->material->list('news', 0, 10);
        dd($res);
    }

    /**
     * 创建菜单
     * @return [type] [description]
     */
    public function createMenu()
    {
        $app = EasyWeChat::officialAccount();
        $buttons = [
            [
                "type" => "view",
                "name" => "植·商城",
                "url"  => "http://www.zhideai.shop"
            ],
            [
                "type" => "view",
                "name" => "得·官网",
                "url"  => "http://zdadjk.creditbrand.org/"
            ],
            [
                "name"       => "艾·中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "植得艾·简介",
                        "url"  => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000007&idx=1&sn=c6208da57c3c6f30a0e3013958355306&chksm=6b97d9655ce0507354acb673d069da9db54956da41342f1f439f58bb3741fd3dd52e0c629023&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "view",
                        "name" => "公司介绍",
                        "url"  => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000019&idx=1&sn=95bfbd084f7be588b6be798b521d0092&chksm=6b97d9715ce050672a5eb3eeb9d312e4ea78016bb9d66ce350f61e8c6fdd401529e52474ded5&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "view",
                        "name" => "模式解析",
                        "url" => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000021&idx=1&sn=8fdbc2bb2d346fd189c4d436eeb6bb70&chksm=6b97d9775ce050613f967e97b021f8115bb201ba3a490b099c8cbcf111acf691c70fecaaa24d&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "view",
                        "name" => "市场前景",
                        "url" => "https://mp.weixin.qq.com/s/87NgYA_yulBK-vYTjJemHg"
                    ],
                    [
                        "type" => "view",
                        "name" => "产品分析",
                        "url" => "https://mp.weixin.qq.com/s/crhQ7qMAfSqzbZKri8V9yQ"
                    ],
                ],
            ],
        ];
        $app->menu->create($buttons);
    }

    public function refundOrder() {
        OrderRefundService::wechatRefundByTransactionId('4200000179201809290617034935', '20180929202117100100102920230320' . time(), 19800, 19800);
    }

    //我的二维码
    public function shareQrCode() {
        $userId = intval(request('id', 0));
        $app = EasyWeChat::officialAccount();
        $data['shareConfig'] = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
        if ($userId) {
            $userInfo = UserService::findById($userId);
            if (!empty($userInfo)) {
                $data['imgSrc'] = env('APP_URL').'/shareImg/' . $userId . '.jpg';
                $data['shareLink'] = env('APP_URL').'/wechat/shareQrCode/'.$userId;//url()->full();
                if ($userInfo->level > 0 && file_exists('./shareImg/' . $userId . '.jpg')) {
                    return view('users.shareQrCode', $data);
                }
            }
            abort(500, '不存在在分享链接');
        } else {
            if (empty(session('user'))) {
                return redirect(env('APP_URL'));
            }
            $userInfo = UserService::findById(session('user')->id);
            if (empty($userInfo) || $userInfo->level < 1) {
                abort('500', '您是游客，没有推荐权限，请先购买商品升级艾达人');
            }
            $data['imgSrc'] = $this->getNewPic();
            $data['shareLink'] = env('APP_URL').'/wechat/shareQrCode/'.$userInfo->id;
            return view('users.shareQrCode', $data);
        }
    }

    //图片合成
    public function getNewPic() {
        $bigImgPath = './images/users/bg.jpg';
        $userId = session('user')->id;
        $imgType = '.jpg';

        $file = './shareImg/' . $userId . $imgType;

        if (file_exists($file)) {
            return env('APP_URL') . '/shareImg/' . $userId . $imgType;
            exit;
        }

        //生成二维码
        $ewmPath = './shareImg/qrcode/'.$userId.'.jpg';
        if (! file_exists($ewmPath)) {
            $app = EasyWeChat::officialAccount();
            $result = $app->qrcode->forever($userId);
            $url = $app->qrcode->url($result['ticket']);
            $content = file_get_contents($url); // 得到二进制图片内容
            file_put_contents($ewmPath, $content);
        }
        $bigImg2 = imagecreatefromstring(file_get_contents($bigImgPath));
        $qCodeImg = imagecreatefromstring(file_get_contents($ewmPath));

        list($bigImgWidth, $bigImgHight, $bigImgType) = getimagesize($bigImgPath);
        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($ewmPath);

        imagecopymerge($bigImg2, $qCodeImg, ($bigImgWidth - $qCodeWidth)/2, ($bigImgHight - $qCodeHight)/2, 0, 0, $qCodeWidth, $qCodeHight, 100);

        //合成文字
        $textFile = './shareImg/textImg/' . time() . '-' . $userId . '.png';
        $text = mb_substr(session('user')->nickname, 0, 10);
        $block = imagecreatetruecolor(600, 100);
        $bg = imagecolorallocatealpha($block, 0, 0, 0, 127); //拾取一个完全透明的颜色
        $color = imagecolorallocate($block, 51, 51, 51); //字体拾色
        imagealphablending($block, false); //关闭混合模式
        imagefill($block, 0, 0, $bg); //填充
        imagefttext($block, 40, 0, (600-mb_strlen($text)*45)/2, 50, $color, realpath('./PingFang.ttc'), $text);
        imagesavealpha($block, true); //设置保存PNG时保留透明通道信息
        imagepng($block, $textFile);

        $textFileImg = imagecreatefromstring(file_get_contents($textFile));
        $this->imagecopymerge_alpha($bigImg2, $textFileImg, ($bigImgWidth - 600)/2, 860, 0, 0, 600, 100, 100);
        switch ($bigImgType) {
            case 1: //gif
                imagegif($bigImg2, $file);
                break;
            case 2: //jpg
                imagejpeg($bigImg2, $file);
                break;
            case 3: //jpg
                imagepng($bigImg2, $file);
                break;
            default:
                break;
        }
        unlink($textFile);
        return env('APP_URL') . '/shareImg/' . $userId . $imgType;
    }

    //合成头像
    public function combineHeader($bigImgPath, $addImgPath, $tarImgPath) {
        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
        list($bigImgWidth, $bigImgHight, $bigImgType) = getimagesize($bigImgPath);

        if (!file_exists($addImgPath)) {
            if (!file_exists('./upload/headerUrl/' . $this->user['uid'] . '.png')) {
                getImage($this->user['headerUrl'], './upload/headerUrl/', $this->user['uid'] . '.png', 1);
            }
            //头像压缩
            $this->thumb('./upload/headerUrl/' . $this->user['uid'] . '.png', $addImgPath, 126, 126);
        }
        $dest_path = $this->rediusImg($addImgPath);
        $addImg = imagecreatefromstring(file_get_contents($dest_path));

        $this->imagecopymerge_alpha($bigImg, $addImg, 14, $bigImgHight - 344, 0, 0, 126, 126, 100);

        switch ($bigImgType) {
            case 1: //gif
                imagegif($bigImg, $tarImgPath);
                break;
            case 2: //jpg
                imagejpeg($bigImg, $tarImgPath);
                break;
            case 3: //jpg
                imagepng($bigImg, $tarImgPath);
                break;
            default:
                break;
        }
        unlink($dest_path);
        return $tarImgPath;
    }

    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        $opacity = $pct;
        // getting the watermark widthW
        $w = imagesx($src_im);
        // getting the watermark height
        $h = imagesy($src_im);
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity);
    }

    function rediusImg($url, $path = './') {
        $w = 126;
        $h = 126; // original size
        $original_path = $url;
        $dest_path = "./upload/tmp/" . time() . '@' . $this->user['uid'] . '.png';
        $src = imagecreatefromstring(file_get_contents($original_path));

        $newpic = imagecreatetruecolor($w, $h);
        $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);
        imagealphablending($newpic, false);
        imagefill($newpic, 0, 0, $transparent);
        imagesavealpha($newpic, true);

        //         imagepng($newpic , './upload/tmp/e.png');
        $r = $w / 2;
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $c = @imagecolorat($src, $x, $y);
                $_x = $x - $w / 2;
                $_y = $y - $h / 2;
                if ((($_x * $_x) + ($_y * $_y)) < ($r * $r)) {
                    imagesetpixel($newpic, $x, $y, $c);
                } else {
                    imagesetpixel($newpic, $x, $y, $transparent);
                }
            }
        }

        imagesavealpha($newpic, true);
        imagepng($newpic, $dest_path);
        imagedestroy($newpic);
        imagedestroy($src);

        return $dest_path;
    }

    //合成背景
    public function combineImg($bigImgPath, $addImgPath, $tarImgPath) {
        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
        list($bigImgWidth, $bigImgHight, $bigImgType) = getimagesize($bigImgPath);

        //背景图压缩
        $tmpFile = './upload/tmp/' . time() . '@' . rand(1000, 9999) . '.png';
        $this->thumb($addImgPath, $tmpFile, $bigImgWidth, 274);

        $addImg = imagecreatefromstring(file_get_contents($tmpFile));

        imagecopymerge($bigImg, $addImg, 0, $bigImgHight - 414, 0, 0, $bigImgWidth, 274, 70);

        list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);

        switch ($bigType) {
            case 1: //gif
                imagegif($bigImg, $tarImgPath);
                break;
            case 2: //jpg
                imagejpeg($bigImg, $tarImgPath);
                break;
            case 3: //jpg
                imagepng($bigImg, $tarImgPath);
                break;
            default:
                break;
        }
        unlink($tmpFile);
        return $tarImgPath;
    }

    public function getEwm($uid, $type = 1, $width = 200, $height = 200){
        if($uid){
            $filename = "./upload/ewm/".$type.'/'.$uid.".png"; //二维码
            if( ! file_exists($filename)){
                $this->load->library ( 'jssdk', array (
                        'appId' => $this->config->item ( 'appId' ),
                        'appSecret' => $this->config->item ( 'appSecret' )
                ) );

                $accessToken = $this->jssdk->getAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accessToken;
                $uid = $this->user_model->get_uid_by_openid($this->openid);

                $m = array(
                        'expire_seconds'    =>    604800,
                        'action_name'        =>    'QR_SCENE',
                        'action_info'        =>    array('scene'=>array('scene_id'=>$uid))
                );
                $res = json_decode($this->jssdk->httpPost($url,json_encode($m)));
                $ticket = $res->ticket;
                $qCodePath = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($res->ticket);
                file_put_contents($filename, file_get_contents($qCodePath));
                $this->thumb($filename, NULL, $width, $height);
            }
            return $filename;
        }
    }

    public  function thumb($dst, $save = NULL, $width = 200, $height = 200) {
        // 首先判断待处理的图片存不存在
        $dinfo = $this->imageInfo($dst);
        if ($dinfo == false) {
            return false;
        }

        // 计算缩放比例
        $calc = min ( $width / $dinfo ['width'], $height / $dinfo ['height'] );

        // 创建原始图的画布
        $dfunc = 'imagecreatefrom' . $dinfo ['ext'];
        $dim = $dfunc ( $dst );

        // 创建缩略画布
        $tim = imagecreatetruecolor ( $width, $height );

        // 创建白色填充缩略画布
        $white = imagecolorallocate ( $tim, 255, 255, 255 );

        // 填充缩略画布
        imagefill ( $tim, 0, 0, $white );

        // 复制并缩略
        $dwidth = ( int ) $dinfo ['width'] * $calc;
        $dheight = ( int ) $dinfo ['height'] * $calc;

        $paddingx = ( int ) ($width - $dwidth) / 2;
        $paddingy = ( int ) ($height - $dheight) / 2;

        imagecopyresampled ( $tim, $dim, $paddingx, $paddingy, 0, 0, $dwidth, $dheight, $dinfo ['width'],

                $dinfo ['height'] );

        // 保存图片
        if (! $save) {
            $save = $dst;
            unlink ( $dst );
        }

        $createfunc = 'image' . $dinfo ['ext'];
        $createfunc ( $tim, $save );

        imagedestroy ( $dim );
        imagedestroy ( $tim );

        return true;
    }

    // imageInfo 分析图片的信息
    // return array()
    public function imageInfo($image) {
        // 判断图片是否存在
        if (! file_exists ( $image )) {
            return false;
        }

        $info = getimagesize ( $image );

        if ($info == false) {
            return false;
        }

        // 此时info分析出来,是一个数组
        $img ['width'] = $info [0];
        $img ['height'] = $info [1];
        $img ['ext'] = substr ( $info ['mime'], strpos ( $info ['mime'], '/' ) + 1 );

        return $img;
    }
}
