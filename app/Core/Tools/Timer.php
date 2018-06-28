<?php

/**
 * Created by PhpStorm.
 * User: nixuan
 * Date: 16/4/15
 * Time: 下午12:54
 */

namespace Core\Tools;

class Timer
{
    protected static $event = [];
    protected static $isOnTimer = FALSE;
    const CONNECT = 1;
    const RECEIVE = 2;
    const LOOPTIME = 1; //设置loop循环的时间片

    /**
     * [add 添加IO事件]
     * @param [type] $socket   [fd]
     * @param [type] $timeout  [second]
     * @param [type] $cli      [description]
     * @param [type] $callback [description]
     */
    public static function add($key, $timeout, $cli, $callback, $params)
    {
        self::init();
        $event = [
            'timeout'  => microtime(TRUE) + $timeout,
            'cli'      => $cli,
            'callback' => $callback,
            'params'   => $params,
        ];
        self::$event[$key] = $event;
    }

    public static function del($key)
    {
        if (isset(self::$event[$key])) {
            unset(self::$event[$key]);
        }
    }

    public static function loop($timer_id)
    {
        /*
            遍历自己的数组，发现时间超过预定时间段，且该IO的状态依然是未回包状态，则走超时逻辑
         */
        if (empty(self::$event)) {
            swoole_timer_clear($timer_id);
        }
        foreach (self::$event as $socket => $e) {
            $now = microtime(TRUE);
            if ($now > $e['timeout']) {
                self::del($socket);
                $cli = $e['cli'];
                $cli->close();
                call_user_func_array($e['callback'], $e['params']);
            }
        }
    }

    /**
     * [init 启动定时器]
     * @return [type] [description]
     */
    public static function init()
    {
        if (!self::$isOnTimer) {
            swoole_timer_tick(1000 * self::LOOPTIME, function ($timer_id) {
                //循环数组，踢出超时情况
                self::loop($timer_id);
                self::$isOnTimer = FALSE;
            });
            self::$isOnTimer = TRUE;
        }
    }
}