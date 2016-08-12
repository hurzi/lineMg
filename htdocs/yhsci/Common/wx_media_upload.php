<?php
/**
 * 图片上传类
 * http参数：
	1，formName：file input name定义up_file
	2，printFormat：json
 *@author grh
 */
error_reporting(0);
ini_set('display_errors', 'off');
header("Content-Type:text/html; charset=utf-8");

define("DOCUMENT_ROOT", dirname(dirname(__FILE__)));
define('FILE_SAVE_PATH', DOCUMENT_ROOT . '/aisuo/Common/weixin/media/');

class UploadImage
{

    public static $urlPath = 'http://wx.hysci.com.cn/yhsci/Common/weixin/media/';
    // public static $uploadPath = './upload'; //上传图片路径
    public static $uploadPath = FILE_SAVE_PATH; // 上传图片本地保存路径
    public static $formName = 'up_file'; // form file name
    public static $printFormat = 'json';

    public static $suffixKey = 'suffix';

    /**
     * 文件上传
     */
    public function uploadFile()
    {
        $suffix = $_POST[self::$suffixKey];
        $resourceFile = $_POST[self::$formName];
        $uploadFile = $_FILES[self::$formName];
        if ((! isset($resourceFile) && empty($resourceFile)) && (! isset($uploadFile) && empty($uploadFile))) {
            echo self::printResult('', 'resource'); // 图片资源不可用
            exit();
        }
        
        $dir_suffix = date('Y/m/');
        self::$uploadPath .= $dir_suffix;
        self::$urlPath .= $dir_suffix;
        
        $name = $suffix ? uniqid() . "." . $suffix : uniqid();
        
        $fileName = self::$uploadPath . $name;
        $fileUrl = self::$urlPath . $name;
        
        if (! is_dir(self::$uploadPath)) {
            @mkdir(self::$uploadPath, 0777, true); // 检查文件目录是否存在
        }
        
        // 保存文件
        if (isset($resourceFile) && ! empty($resourceFile)) {
            $size = file_put_contents($fileName, $resourceFile);
            if (! $size) {
                echo self::printResult('', 'save'); // 保存文件失败
                exit();
            }
        } else {
            if (empty($uploadFile)) {
                echo self::printResult('', 'resource'); // 图片资源不可用
                exit();
            }
            if ($uploadFile["size"] == 0 || $uploadFile["error"] > 0) {
                echo self::printResult('', 'resource'); // 图片资源不可用
                exit();
            }
            if (! is_uploaded_file($uploadFile['tmp_name'])) {
                echo self::printResult('', 'invalid'); // 图片不存在
                exit();
            }
            if (! move_uploaded_file($uploadFile["tmp_name"], $fileName)) {
                echo self::printResult('', 'move'); // 移动文件出错
                exit();
            }
        }
        
        echo self::printResult($fileUrl);
    }

    public function getFileType($fileName)
    {
        $type = substr($fileName, strrpos($fileName, '.'));
        return strtolower(trim($type));
    }

    /**
     * 输出返回信息
     * 
     * @param string $file_path
     *            上传文件的路径
     * @param string $errorType
     *            错误类型
     */
    function printResult($file_path, $errorType = null)
    {
        $result = array('error' => 0, 'msg' => '', 'file' => '');
        if ($errorType === null) {
            $result['file'] = $file_path;
        } else {
            switch ($errorType) {
                case "size":
                    $result['error'] = 1;
                    $result['msg'] = '上传文件过大';
                    break;
                case 'type':
                    $result['error'] = 2;
                    $result['msg'] = '上传文件类型错误';
                    break;
                case 'exsit':
                    $result['error'] = 3;
                    $result['msg'] = '上传文件已存在';
                    break;
                case 'invalid':
                    $result['error'] = 4;
                    $result['msg'] = '上传文件无效';
                    break;
                case 'resource':
                    $result['error'] = 5;
                    $result['msg'] = '文件资源不可用';
                    break;
                case 'move':
                    $result['error'] = 6;
                    $result['msg'] = '移动文件出错';
               case 'save':
                    $result['error'] = 7;
                    $result['msg'] = '保存文件失败';
                default:
                    $result['error'] = 8;
                    $result['msg'] = '上传文件失败';
            }
        }
        echo json_encode($result);
        exit();
    }

    private function genCallbackUrl($url, $result)
    {
        $param = array('callback' => self::$callback, 'result' => base64_encode(json_encode($result)));
        $index = strpos($url, '?');
        return $url . ($index === false ? '?' : '&') . http_build_query($param);
    }
}

$img = new UploadImage();
$img->uploadFile();
