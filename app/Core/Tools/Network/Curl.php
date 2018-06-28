<?php

/**
 * Created by PhpStorm.
 * User: yuetianshuang@imcoming.cn
 * Date: 16/3/16
 * Time: 下午17:40
 */

namespace Core\Tools\Network;

use Core\Tools\SeasLogPlugin;

class Curl
{
    const MODE_NORMAL = 1;
    const MODE_TEST = 2;
    const CURL_TIMEOUT = 3000;

    public static $mode = Curl::MODE_TEST;

    protected static function curl($url, $protocol, $getParams = [], $postParams = [], $headerParams = [], $useSSL = FALSE, $paramsToJson = FALSE)
    {
        $startAt = SeasLogPlugin::getMicroTime();
        $url = self::buildUrl($url, $getParams);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
//        if (self::$mode == Curl::MODE_TEST && PHP_OS == 'Darwin') {
//            curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');
//            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//        }

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $useSSL);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $useSSL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);

        switch ($protocol) {
            case 'get':
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json; charset=utf-8',
                ]);
                break;
            case 'put':
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($postParams)]);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postParams);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postParams));
                curl_setopt($curl, CURLINFO_CONTENT_TYPE, 'application/x-www-form-urlencoded');
            case 'delete':
                if ($protocol == 'post') {
                    curl_setopt($curl, CURLOPT_POST, 1);
                } else {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                }
                if (is_array($headerParams) && count($headerParams)) {
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerParams);
                }
                if ($paramsToJson) {
                    $postParams = json_encode($postParams);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($postParams)]);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postParams);
                } else {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postParams));
                }
                break;

            default :
                return "";
        }
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
        }
        curl_close($curl);
        if (isset($put_data)) {
            fclose($put_data);
        }
        SeasLogPlugin::requestLog('curl', $url, $postParams, $data, SeasLogPlugin::caleCostTime($startAt));
        return $data;
    }

    protected static function buildUrl($baseUrl, $params, $encodeFlag = FALSE)
    {
        if (is_array($params) && !empty($params)) {
            $url = [];
            $url[] = $baseUrl;
            $url[] = '?';
            $query = http_build_query($params);
            $url[] = $encodeFlag ? urlencode($query) : $query;
            $url = implode("", $url);
        } else {
            $url = $baseUrl;
        }
        return $url;
    }

    /******************************************************************************
     * public functions
     ******************************************************************************/

    /**
     * @param $url
     * @param array $getParams
     * @param array $postParams
     * @param null $headerParams
     * @param bool $decode
     * @param bool $paramsToJson
     * @return mixed|null|string
     */
    public static function post($url, $getParams = [], $postParams = [], $headerParams = NULL, $decode = TRUE, $paramsToJson = FALSE)
    {
        try {
            $result = self::curl($url, 'post', $getParams, $postParams, $headerParams, FALSE, $paramsToJson);
            if ($decode) {
                return json_decode($result, TRUE);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            SeasLogPlugin::saveError('curl请求错误', 'curl_error', $e);
            return FALSE;
        }
    }

    /**
     * @param $url
     * @param array $getParams
     * @param array $postParams
     * @param null $headerParams
     * @param bool $decode
     * @return mixed|null
     */
    public static function postJSON($url, $getParams = [], $postParams = [], $headerParams = NULL, $decode = TRUE)
    {
        return self::post($url, $getParams, $postParams, $headerParams, $decode, TRUE);
    }

    /**
     * 以Application/json为内容类型提交
     * @param $url
     * @param array $getParams
     * @param array $postParams
     * @param null $headerParams
     * @param bool $decode
     * @param bool $paramsToJson
     * @return mixed|null|string
     */
    public static function postAppJson($url, $getParams = [], $postParams = [], $headerParams = NULL, $decode = TRUE, $paramsToJson = FALSE)
    {
        try {
            $result = self::curl($url, 'postJson', $getParams, $postParams, $headerParams, FALSE, $paramsToJson);
            if ($decode) {
                return json_decode($result, TRUE);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            SeasLogPlugin::saveError('curl请求错误', 'curl_error', $e);
            return FALSE;
        }
    }

    public static function get($url, array $getParams = [], $headerParams = NULL, $decode = TRUE)
    {
        try {
            $result = self::curl($url, 'get', $getParams, $headerParams, FALSE);
            if ($decode) {
                return json_decode($result, TRUE);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            SeasLogPlugin::saveError('curl请求错误', 'curl_error', $e);
            return FALSE;
        }
    }

    public static function delete($url, array $getParams = [], $postParams = [], $headerParams = NULL, $decode = FALSE, $paramsToJson = FALSE)
    {
        try {
            $result = self::curl($url, 'delete', $getParams, $postParams, $headerParams, FALSE, $paramsToJson);
            if ($decode) {
                return json_decode($result, TRUE);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            SeasLogPlugin::saveError('curl请求错误', 'curl_error', $e);
            return FALSE;
        }
    }

    public static function sslPost($url, $getParams = [], $postParams = [], $headerParams = NULL)
    {
        return self::curl($url, 'post', $getParams, $postParams, $headerParams, TRUE);
    }

    public static function sslGet($url, array $getParams = [], $headerParams = NULL)
    {
        return self::curl($url, 'get', $getParams, $headerParams, TRUE);
    }

    public static function sslDelete($url, array $getParams = [], $headerParams = NULL)
    {
        return self::curl($url, 'delete', $getParams, $headerParams, TRUE);
    }
}

