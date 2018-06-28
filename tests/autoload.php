<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/7/12
 * Time: 下午3:23
 */
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__) . DS);
define('TEST_PATH', BASE_PATH . 'tests' .DS);
define('APP_PATH', BASE_PATH . 'app' . DS);

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__) . DS);
define('APP_PATH', BASE_PATH . 'app' . DS);
define('CORE_PATH', APP_PATH . 'Core' . DS);
define('TOOLS_PATH', APP_PATH . 'Tools' . DS);
define('CONFIG_PATH', APP_PATH . 'config' . DS);
define('LOG_PATH', dirname(BASE_PATH) . DS . 'logs' . DS);
define('VIEWS_PATH', APP_PATH . 'views' . DS);
define('COMPILED_PATH', BASE_PATH . 'runtime' . DS . 'compiled' . DS);

spl_autoload_register(
    function ($className){
        if(is_file(TEST_PATH . $className . '.php')){
            require TEST_PATH . $className . '.php';
        }
    }
);