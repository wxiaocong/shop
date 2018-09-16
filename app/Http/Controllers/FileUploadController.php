<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use OSS\Core\OssException;
use OSS\OssClient;

class FileUploadController extends Controller
{
    // 5 * 1024 * 1024 = 5242880
    const MAX_FILE_SIZE = 5242880;

    private $stateInfo;          //上传状态信息
    private $uploadToOss = true; //文件域名
    private $signedUrl;          //OSS的签名url

    public function uploadFile()
    {
        $fileName = '';
        $dataType = request('dataType');

        switch ($dataType) {
            case 'file':
                $fileName   = time() . rand(1, 10000);
                $file       = Input::file('file');
                $fileSuffix = $file->getClientOriginalExtension();
                $fileName .= '.' . $fileSuffix;
                $fullName = 'files/' . $fileName;

                // 文件上传到OSS
                if ($this->ossUploadFile(env('OSS_FILE_BUCKET'), $fullName, $file)) {
                    $this->stateInfo = 'SUCCESS';
                } else {
                    $this->uploadToOss = false;
                    if (!move_uploaded_file($file, $fullName)) {
                        $this->stateInfo = '文件保存时出错';
                    }
                }

                $fileName = env('OSS_FILE_URL') . '/' . $fullName;
                break;
            case 'base64':
                $fileName = $this->base64ToImage(request('file'));
                break;
        }

        if ($this->stateInfo == 'SUCCESS') {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('上传成功！'),
                'fileName' => $fileName,
                'url'      => '',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array($this->stateInfo),
                'fileName' => $fileName,
                'url'      => '',
            ));
        }
    }
    
    public function uploadLocalFile()
    {
        $user = session('user');
        
        $fileName = uniqid($user['id']);
        $filePath = 'files';
        $dataType = request('dataType');
        
        switch ($dataType) {
            case 'file':
                $file       = Input::file('file');
                $fileSuffix = $file->getClientOriginalExtension();
                $fileName .= '.' . $fileSuffix;
                $file->move($filePath, $fileName);
                break;
                
            case 'base64':
                $file  = request('file');
                $image = base64_decode($file);
                $fileName .= '.jpg';
                $filePath .= '/' . $fileName;
                file_put_contents($filePath, $image);
                break;
        }
        
        return response()->json(array(
            'code'        => 200,
            'messages'    => array('上传成功！'),
            'fileName'    => $fileName,
            'redirectUrl' => '',
        ));
    }

    /**
     * 处理base64编码的图片上传
     * @param $base64Data
     * @return string
     */
    private function base64ToImage($base64Data)
    {
        $img      = base64_decode($base64Data);
        $fileName = time() . rand(1, 10000) . '.jpg';
        $fullName = 'files/' . $fileName;

        // 文件上传到OSS
        if ($this->ossUploadFile(env('OSS_FILE_BUCKET'), $fullName, $img)) {
            $this->stateInfo = $this->stateMap[0];
        } else {
            $this->uploadToOss = false;
            if (!file_put_contents($fullName, $img)) {
                $this->stateInfo = '输入输出错误';
                return null;
            }
        }

        return env('OSS_FILE_URL') . '/' . $fullName;
    }

    /**
     * 文件上传到OSS
     *
     */
    private function ossUploadFile($bucket, $object, $filePath)
    {
        $ossClient = $this->getOssClient();
        try {
            if (strpos($object, "/") == 0) {
                $object = substr($object, 1);
            }

            $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (OssException $e) {
            $this->stateInfo = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * 根据Config配置，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    private function getOssClient()
    {
        try {
            $ossClient = new OssClient(env('OSS_ACCESS_ID'), env('OSS_ACCESS_KEY'), env('OSS_ENDPOINT'), false);
        } catch (OssException $e) {
            $this->stateInfo = $e->getMessage();
            return null;
        }

        return $ossClient;
    }

}
