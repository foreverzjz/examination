<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 下午2:16
 */

namespace Enumerations;

class CacheConst
{
    CONST CACHE_CONNECTION = 'redis';

    CONST LOGIN_PASS_ERROR = 'examination:login:uid|%s:error';
    CONST LOGIN_BAN = 'examination:login:uid|%s:ban';

    CONST LOGIN_UID = 'examination:login:uid:%s';
    CONST LOGIN_TOKEN = 'examination:login:token:%s';

}