<?php
error_reporting(E_ALL & ~E_NOTICE);
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__) . DS);
define('APP_PATH', BASE_PATH . 'app' . DS);
define('CORE_PATH', APP_PATH . 'Core' . DS);
define('TOOLS_PATH', APP_PATH . 'Tools' . DS);
define('CONFIG_PATH', APP_PATH . 'Config' . DS);
define('LOG_PATH', dirname(BASE_PATH) . DS . 'logs' . DS);
define('VIEWS_PATH', APP_PATH . 'Views' . DS);
define('COMPILED_PATH', BASE_PATH . 'Runtime' . DS . 'compiled' . DS);

require CORE_PATH . 'bootstrap.php';
Core\bootstrap::runConsoleApp($argv);
