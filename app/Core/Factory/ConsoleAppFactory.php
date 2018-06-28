<?php
/**
 * 控制台应用程序注册器
 * User: Peter Pan
 * Date: 2017/6/29
 * Time: 22:55
 */

namespace Core\Factory;

use Core\Tools\Medoo;
use Phalcon\Db\Profiler;
use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault\Cli as CliDi;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Mvc\Model\MetaData\Strategy\Annotations as StrategyAnnotations;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Core\Tools\MyRedis as Redis;
use Core\Tools\Validator;

class ConsoleAppFactory extends CliDi
{
    public function __construct($config)
    {
        parent::__construct();

        $this->setShared('config', $config);
        $this->bindServices();
    }

    protected function bindServices()
    {
        $reflection = new \ReflectionObject($this);
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            if ((strlen($method->name) > 10) && (strpos($method->name, 'initShared') === 0)) {
                $this->set(lcfirst(substr($method->name, 10)), $method->getClosure($this));
                continue;
            }
            if ((strlen($method->name) > 4) && (strpos($method->name, 'init') === 0)) {
                $this->set(lcfirst(substr($method->name, 4)), $method->getClosure($this));
            }
        }
    }

    protected function initSeasLogs()
    {
        $seasLog = new Logs();
        $LogPath = dirname(APP_PATH) . DS . 'log' . DS . 'seas_log';
        echo $LogPath;
        logs::requestStart($LogPath);
        return $seasLog;
    }

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    protected function initDb()
    {
        $config = $this->get('config')->get('database')->toArray();
//        $eventsManager = new EventsManager;
//        $profiler = $this->getProfiler();

        //监听所有的db事件
//        $eventsManager->attach('db', function($event, $connection) use ($profiler) {
//            //一条语句查询之前事件，profiler开始记录sql语句
//            if ($event->getType() == 'beforeQuery') {
//                echo $connection->getSQLStatement() ."\r\n--------------\r\n";
//                $profiler->startProfile($connection->getSQLStatement());
//            }
//            //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
//            if ($event->getType() == 'afterQuery') {
//                $profiler->stopProfile();
//            }
//        });

        $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
        unset($config['adapter']);

        $connection = new $dbClass($config);

//        $connection->setEventsManager($eventsManager);

        return $connection;
    }

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    protected function initDbSlave()
    {
        $config = $this->get('config')->get('databaseSlave')->toArray();
//        $eventsManager = new EventsManager;
//        $profiler = $this->getProfiler();

        //监听所有的db事件
//        $eventsManager->attach('db', function($event, $connection) use ($profiler) {
//            //一条语句查询之前事件，profiler开始记录sql语句
//            if ($event->getType() == 'beforeQuery') {
//                echo $connection->getSQLStatement() ."\r\n--------------\r\n";
//                $profiler->startProfile($connection->getSQLStatement());
//            }
//            //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
//            if ($event->getType() == 'afterQuery') {
//                $profiler->stopProfile();
//            }
//        });

        $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
        unset($config['adapter']);

        $connection = new $dbClass($config);

//        $connection->setEventsManager($eventsManager);

        return $connection;
    }

    protected function initSharedMDb()
    {
        $config = $this->get('config')->get('database')->toArray();
        $mConfig = [
            // 必须配置项
            'database_type' => $config['adapter'],
            'database_name' => $config['dbname'],
            'server' => $config['host'],
            'username' => $config['username'],
            'password' => $config['password']
        ];
        unset($config);
        return new Medoo($mConfig);
    }
    protected function initSharedMDbSlave()
    {
        $config = $this->get('config')->get('databaseSlave')->toArray();
        $mConfig = [
            // 必须配置项
            'database_type' => $config['adapter'],
            'database_name' => $config['dbname'],
            'server' => $config['host'],
            'username' => $config['username'],
            'password' => $config['password']
        ];
        unset($config);
        return new Medoo($mConfig);
    }

    protected function initRedis()
    {
        $config = $this->get('config')->get('redis')->toArray();
        $frontCache = new FrontendData(["lifetime" => 172800]);
        $cache = new Redis($frontCache, $config);
        return $cache;
    }

    /**
     * If the configuration specify the use of metadata adapter use it or use memory otherwise
     */
    protected function initModelsMetadata()
    {
        $metadata = new MetaData([
            'lifetime' => 86400,
            'prefix' => 'meta',
        ]);
        $metadata->setStrategy(new StrategyAnnotations());
        return $metadata;
    }

    /**
     * Factory a user component
     */
    protected function initElements()
    {
        return new Elements();
    }

    protected function initSharedValidator()
    {
        return new Validator();
    }

}