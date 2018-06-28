<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/9/12
 * Time: 上午11:07
 */

namespace Core\Tools;

use Library\Common;
use Phalcon\Di;
use \Exception;

class ErrorHandler
{
    const ERROR_CODE_TOKEN_INVALID = -80401;
    const ERROR_CODE_DUPLICATE_ENTRY = -80162;
    const ERROR_CODE_PARAM_VALIDATE = -80101;
    const ERROR_CODE_RECORD_NOT_EXIST = -80104;
    const ERROR_CODE_SIGN_INVALID = -80102;
    const ERROR_CODE_SIGN_ERROR = -80103;
    static $message = '';
    static $flag = -1;


    static function exception(Exception $exception, $param = NULL, int $level = 1)
    {

        $logContent = '--------------------------------------------' . PHP_EOL .
            'Time:' . date('Y-m-d H:i:s') . PHP_EOL .
            'Message:(' . $exception->getCode() . ')' . $exception->getMessage() . PHP_EOL .
            'File:' . $exception->getFile() . '(' . $exception->getLine() . ')' . PHP_EOL .
            'Trace:' . $exception->getTraceAsString() . PHP_EOL;
        $errorModel = 'console_error';
        if (Di::getDefault()->get('config')->debug->app) {
            //输出错误信息
            if (is_null($_SERVER['SHELL'])) {
                $errorModel = 'www_error';
                header('HTTP/1.1 500 Internal Server Error');
                echo str_replace(PHP_EOL, '<br />', $logContent);
                exit;
            } else {
                echo $logContent;
            }
        } else {
            if (is_null($_SERVER['SHELL'])) {
                $errorModel = 'www_error';
                SeasLogPlugin::saveError($logContent, $errorModel, $param);
                if ($level == E_ERROR) {
                    header('HTTP/1.1 500 Internal Server Error');
                    echo '服务器内部错误！';
                    SeasLogPlugin::accessLog($_POST, $_GET, $exception->getMessage());
                    exit();
                }
                if ($level == E_WARNING) {
                    $error = ['message' => $exception->getMessage(), 'flag' => Common::unAbs($exception->getCode()), 'result' => FALSE];
                    echo json_encode($error);
                    SeasLogPlugin::accessLog($_POST, $_GET, $exception->getMessage());
                    exit();
                }
                if ($level == E_NOTICE) {
                    SeasLogPlugin::accessLog($_POST, $_GET, $exception->getMessage());
                }
            }

        }


    }

    static function saveDebugLog($message, $fn, $fnParam)
    {
        $logContent = PHP_EOL . '--------------------------------------------' . PHP_EOL .
            'Time:' . date('Y-m-d H:i:s') . PHP_EOL .
            'Message:' . $message . PHP_EOL .
            'Function:' . $fn . PHP_EOL .
            'Trace:' . var_export($fnParam, TRUE) . PHP_EOL;
        SeasLogPlugin::saveDebugLog($logContent);
    }

    static function displayAndBreak()
    {

    }

    /**
     * 设置错误信息
     */
    static public function setErrorInfo()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            self::$message = '未知错误';
            self::$flag = -1;
        } elseif (count($args) == 1) {
            if (is_numeric($args[0])) {
                self::$message = '未知错误';
                self::$flag = (int)$args[0];
            } else {
                self::$message = $args[0];
                self::$flag = -1;
            }
        } else {
            if (is_numeric($args[0])) {
                self::$message = $args[1];
                self::$flag = $args[0];
            } else {
                self::$message = $args[0];
                self::$flag = $args[1];
            }
            if (!empty($args[2])&&is_object($args[2])){
                $error = NULL;
                $obj = $args[2];
                if(count($obj->getMessages())>0){
                    foreach ($obj->getMessages() as $message)
                    {
                        $error.= $message->getMessage().PHP_EOL;
                    }
                    SeasLogPlugin::saveError($error,'error',func_get_args());
                }
            }
        }
    }

    /**
     * 清空错误信息
     */
    static public function clearErrorInfo()
    {
        self::$flag = -1;
        self::$message = '';
    }
}