<?php

/**
 * 引导程序
 * User: Peter Pan
 * Date: 2017/6/30
 * Time: 22:28
 */

namespace Core;

use Core\Tools\ErrorHandler;
use Phalcon\Mvc\Application;
use Phalcon\CLI\Console as ConsoleApp;
use Phalcon\Loader;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Core\Tools\SeasLogPlugin;
use Core\Factory\ApplicationFactory;
use Core\Factory\ConsoleAppFactory;

class bootstrap
{
    const CONSOLE_APP_NAMESPACE = '\\Tasks\\';
    const DEFAULT_TASK = 'Main';
    const DEFAULT_TASK_ACTION = 'Main';

    /**
     * 获取Config配置
     * @return ConfigIni
     */
    static public function getConfig()
    {
        $config = new ConfigIni(CONFIG_PATH . 'Config.ini');
        return $config;
    }

    /**
     * 获取Setting配置
     * @return ConfigIni
     */
    static public function getSetting()
    {
        $config = new ConfigIni(CONFIG_PATH . 'Setting.ini');
        return $config;
    }

    /**
     * 命名空间自动加载
     */
    static public function autoLoad()
    {
        $loader = new Loader();
        $autoLoadDir = include CONFIG_PATH . 'AutoloadMaps.php';
        //$loader->registerDirs($autoLoadDir->dir)->register();
        $loader->registerNamespaces($autoLoadDir->namespace)->register();
    }

    /**
     * 处理控制台入参
     * @param array $argv
     * @param string $domain
     * @return mixed
     */
    static public function processArguments(array $argv, string $domain = '')
    {
        $domainName = empty($domain) ? self::CONSOLE_APP_NAMESPACE : self::CONSOLE_APP_NAMESPACE . ucfirst($domain) . '\\';
        if (count($argv) == 1) {
            $arguments['task'] = $domainName . self::DEFAULT_TASK;
            $arguments['action'] = self::DEFAULT_TASK_ACTION;
        } else {
            foreach ($argv as $k => $arg) {
                if ($k == 1) {
                    $arguments['task'] = $domainName . ucfirst($arg);
                } elseif ($k == 2) {
                    $arguments['action'] = $arg;
                } elseif ($k >= 3) {
                    $params[] = $arg;
                }
            }
        }
        if (count($params) > 0) {
            $arguments['params'] = $params;
        }
        return $arguments;
    }

    /**
     * 启动一个标准应用程序
     */
    static public function runApplication()
    {
        $config = self::getConfig();
        self::autoLoad();
        SeasLogPlugin::requestStart(LOG_PATH . 'seas_log');
        try {
            $application = new Application(new ApplicationFactory($config));
            echo $application->handle(!empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : NULL)->getContent();
        } catch (\Exception $e) {
            ErrorHandler::exception($e);
        }
    }

    /**
     * 启动一个控制台应用程序
     * @param array $argv 控制台启动时传入的参数(php cli.php [控制器] [方法])
     * @param string $domain
     */
    static public function runConsoleApp(array $argv, string $domain = '')
    {
        $config = self::getConfig();
        self::autoLoad();
        SeasLogPlugin::requestStart(LOG_PATH . 'seas_log' . DS);
        try {
            $application = new ConsoleApp(new ConsoleAppFactory($config));
            $application->handle(self::processArguments($argv, $domain));
        } catch (\Exception $e) {
            ErrorHandler::exception($e);
        }
    }
}
