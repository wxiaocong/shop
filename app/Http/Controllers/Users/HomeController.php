<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersRequest;
use App\Services\Users\UserService;
use EasyWeChat;
use Session;

class HomeController extends Controller {
    public function index() {
        $data['userInfo'] = UserService::findById(session('user')->id);
        $data['levelArr'] = config('statuses.user.levelState');
        return view('users.home', $data);
    }

    //个人信息
    public function show() {
        $userInfo = UserService::findById(session('user')->id);
        return view('users.person', array('userInfo' => $userInfo));
    }

    //保存个人信息
    public function update(UsersRequest $request, int $id) {
        $id = session('user')->id;
        $result = UserService::existColumn('nickname', $request['nickname'], $id);
        if ($result) {
            return response()->json($result);
        }
        if ($request['sex'] == '男') {
            $request['sex'] = 1;
        } elseif ($request['sex'] == '女') {
            $request['sex'] = 2;
        } else {
            $request['sex'] = 0;
        }
        //日期控件默认值问题，结尾有空格
        $request['birthday'] = empty(trim($request['birthday'])) ? null : trim($request['birthday']);

        if (UserService::getById($id)->update($request->all())) {
            Session(array('user' => UserService::findById($id)));
            return response()->json(array(
                'code' => 200,
                'messages' => array('保存成功'),
                'url' => '/home',
            ));
        } else {
            return response()->json(array(
                'code' => 500,
                'messages' => array('保存失败'),
                'url' => '',
            ));
        }
    }

    //我的二维码
    public function shareQrCode() {
        $data['imgSrc'] = $this->getNewPic();
        //生成分享配置
        $data['shareConfig'] = '';
        if (isWeixin()) {
            $app = EasyWeChat::officialAccount();
            $data['shareConfig'] = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
        }
        return view('users.shareQrCode', $data);
    }

    //图片合成
    public function getNewPic() {
        $bigImgPath = './images/users/bg.jpg';
        $userId = session('user')->id;
        $imgType = '.jpg';

        $file = './shareImg/' . $userId . $imgType;

        if (file_exists($file)) {
            return env('APP_URL') . $file;
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

        imagecopymerge($bigImg2, $qCodeImg, ($bigImgWidth - $qCodeWidth)/2, $bigImgHight - $qCodeHight - 218, 0, 0, $qCodeWidth, $qCodeHight, 100);

        //合成文字
        $textFile = './shareImg/textImg/' . time() . '-' . $userId . '.png';
        $text = session('user')->nickname;
        $block = imagecreatetruecolor(120, 100);
        $bg = imagecolorallocatealpha($block, 0, 0, 0, 127); //拾取一个完全透明的颜色
        $color = imagecolorallocate($block, 51, 51, 51); //字体拾色
        imagealphablending($block, false); //关闭混合模式
        imagefill($block, 0, 0, $bg); //填充
        imagefttext($block, 30, 0, 10, 36, $color, realpath('./PingFang.ttc'), $text);
        imagesavealpha($block, true); //设置保存PNG时保留透明通道信息
        imagepng($block, $textFile);

        $textFileImg = imagecreatefromstring(file_get_contents($textFile));
        $this->imagecopymerge_alpha($bigImg2, $textFileImg, 210, $bigImgHight - 820, 0, 0, 120, 100, 100);
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
        // unlink($bfile);
        // unlink($hfile);
        // unlink($textFile);
        return env('APP_URL') . $file;
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
