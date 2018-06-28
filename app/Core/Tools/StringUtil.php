<?php
/**
 * Created by PhpStorm.
 * User: tsyue
 * Date: 16/4/7
 * Time: 下午1:33
 */

namespace Core\Tools;

class StringUtil
{
    public static function random($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function length($string = NULL)
    {
        preg_match_all("/./us", $string, $match);
        return count($match[0]);
    }
}