<?php
/**
 * Created by PhpStorm.
 * User: miaoyanhong
 * Date: 2017/9/12
 * Time: 下午2:46
 */
namespace Core\Tools;

use \stdClass;
use \finfo;
use \Phalcon\Di;

class Resupload
{
    public $resUrl;
    private $allowMimeType;


    public function __construct()
    {
        $config = Di::getDefault()->get('config');
        $baseUrl = $config->webService->imgsvc;
        if(empty($baseUrl)) {
            $baseUrl = 'http://imgsvc.anlaiye.com.cn';
        }
        $this->resUrl = $baseUrl . '/img/upload/multipart';
        $this->allowMimeType = array(
            'image/jpeg',
            'image/gif',
            'image/png',
            'image/svg+xml',
            'audio/mpeg',
            'video/3gpp',
            'video/mp4',
            'string/w',
        );
    }

    public function fileUpload($filePath, $fileName, $fileCate)
    {


        $return = new stdClass();
        $return->flag = -1;
        $return->result = FALSE;
        $return->message = '';
        $return->path = '';
        // $uploadFile = realpath($filePath);

        if(!file_exists($filePath)){
            $return->message = '图片不存在';
            return $return;
        }
        $uploadFile = $filePath;
        $fi = new finfo(FILEINFO_MIME_TYPE);

        $miniType = strtolower($fi->file($uploadFile));

        if (!in_array($miniType, $this->allowMimeType)) {
            $return->message = '文件类型不在允许范围！';
            return $return;
        }

        $curlFile = curl_file_create($uploadFile, $miniType, $fileName);
        $postData = array(
            'file' => $curlFile
        );
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->resUrl);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        curl_setopt($curlHandle, CURLINFO_CONTENT_TYPE, 'multipart/form-data');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 1000000);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curlHandle, CURLOPT_INFILESIZE, filesize($uploadFile));
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);
        $jsonData = json_decode($result);
        if(is_null($jsonData)){
            $return->message = '远程上传失败！';
            return $return;
        }
        if(!empty($jsonData->error) || empty($jsonData->url)){
            $return->message = '远程上传失败！';
            return $return;
        }
        $return->path = $jsonData->url;
        $return->result = TRUE;

        return $return;
    }
}