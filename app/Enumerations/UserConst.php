<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 下午2:02
 */

namespace Enumerations;

class UserConst
{
    CONST STATUS_NO_ACTIVE = 0;   //未激活
    CONST STATUS_NORMAL = 1;      //正常
    CONST STATUS_BAN    = 2;      //禁用
    CONST STATUS_DELETE = 3;      //删除

    CONST LOGIN_MAX_ERROR_TIME = 5;  //登录密码错误最大次数
}