<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/7/16
 * Time: 下午1:53
 */

namespace Core\Tools\Crypt;

class Aes
{
    const METHOD = 'aes-128-cbc';
    /**
     * 去除URL混淆字符的 base64_encode
     * @param string $string
     * @return string
     */
    static public function enb64($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('__2B', '__2F', ''), $data);
        return $data;
    }

    /**
     * 解析除URL混淆字符的 base64_decode
     * @param string $string
     * @return string
     */
    static public function deb64($string)
    {
        $data = str_replace(array('__2B', '__2F'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    static public function encrypt($string, $key, $iv)
    {
        if (empty($string)) {
            return '';
        }
        $string = self::enb64(openssl_encrypt($string,self::METHOD,$key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv));
        return $string;
    }

    static public function decrypt($string, $key, $iv)
    {
        if (empty($string)) {
            return '';
        }
        $string = openssl_decrypt(self::deb64($string),self::METHOD,$key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv);
        $string = rtrim(rtrim($string), "\x00..\x1F");
        return $string;
    }

}
