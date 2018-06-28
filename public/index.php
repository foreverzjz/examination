<?php
date_default_timezone_set('Asia/ShangHai');

error_reporting(E_ALL & ~E_NOTICE);
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__) . DS);
define('APP_PATH', BASE_PATH . 'app' . DS);
define('CORE_PATH', APP_PATH . 'Core' . DS);
define('TOOLS_PATH', CORE_PATH . 'Tools' . DS);
define('CONFIG_PATH', APP_PATH . 'Config' . DS);
define('LOG_PATH', dirname(BASE_PATH) . DS . 'logs' . DS);
define('VIEWS_PATH', APP_PATH . 'Views' . DS);
define('COMPILED_PATH', BASE_PATH . 'runtime' . DS . 'compiled' . DS);
define('PUBLIC_PATH', BASE_PATH . 'public' . DS);

if (isset($_GET['_url'])) {
    $_SERVER['REQUEST_URI'] = strtolower($_SERVER['REQUEST_URI']);
    $_GET['_url'] = strtolower($_GET['_url']);
}
require CORE_PATH . 'bootstrap.php';

Core\bootstrap::runApplication();