<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersRequest;
use App\Services\Users\UserService;
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
	public function qrCode() {

	}

	//图片合成
	public function getNewPic() {
		$bigImgPath = elixir('images/users/bg.jpg');

		$imgName = session('user')->id;
		$imgType = '.jpg';

		$file = './qrcode/' . $imgName . $imgType;

		if (file_exists($file)) {
			echo base_url() . $file;
			exit;
		}

		//合成背景
		// $bfile = './upload/tmp/' . time() . '-' . $this->user['uid'] . $imgType;
		// $bigImgPath = $this->combineImg($bigImgPath, './static/images/comBack.png', $bfile);
		//合成头像
		// $hfile = './upload/tmp/' . time() . '&' . $this->user['uid'] . $imgType;
		// $headerPath = './upload/headerUrl/2/' . $this->user['uid'] . '.png';
		// $bigImgPath = $this->combineHeader($bigImgPath, $headerPath, $hfile);

		//合成二维码
		$ewmPath = $this->getEwm($this->user['uid'], 2, 180, 180); //二维码

// 				$ewmPath = "./upload/ewm/2/".$this->user['uid'].".png"; //二维码
		// 				$this->thumb($ewmPath,NULL,140,140);
		$bigImg2 = imagecreatefromstring(file_get_contents($bigImgPath));
		$qCodeImg = imagecreatefromstring(file_get_contents($ewmPath));

		list($bigImgWidth, $bigImgHight, $bigImgType) = getimagesize($bigImgPath);
		list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($ewmPath);

		imagecopymerge($bigImg2, $qCodeImg, $bigImgWidth - $qCodeWidth - 64, $bigImgHight - $qCodeHight - 218, 0, 0, $qCodeWidth, $qCodeHight, 100);

		//合成文字
		$textFile = './upload/tmp/' . time() . '+' . $this->user['uid'] . '.png';
		$text = PHP_EOL . $this->user['realName'] . '(' . $this->user['phone'] . ')' . PHP_EOL . $this->user['rank'] . PHP_EOL . $this->user['remark'];
		$block = imagecreatetruecolor(300, 200);
		$bg = imagecolorallocatealpha($block, 0, 0, 0, 127); //拾取一个完全透明的颜色
		$color = imagecolorallocate($block, 51, 51, 51); //字体拾色
		imagealphablending($block, false); //关闭混合模式
		imagefill($block, 0, 0, $bg); //填充
		imagefttext($block, 24, 0, 10, 20, $color, './PingFang.ttc', $text);
		imagesavealpha($block, true); //设置保存PNG时保留透明通道信息
		imagepng($block, $textFile);

		$textFileImg = imagecreatefromstring(file_get_contents($textFile));
		$this->imagecopymerge_alpha($bigImg2, $textFileImg, 160, $bigImgHight - 400, 0, 0, 300, 200, 100);
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
		unlink($bfile);
		unlink($hfile);
		unlink($textFile);
		echo base_url() . $file;
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

// 		imagepng($newpic , './upload/tmp/e.png');
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
}
