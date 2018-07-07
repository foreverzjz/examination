<?php

/**
 * 标准应用程序注册器
 * Author: Peter Pan
 * Date: 2017/7/1
 * Time: 23:38
 */

namespace Core\Factory;


use Core\Tools\Medoo;
use Phalcon\Db\Profiler;
use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Mvc\Model\MetaData\Apc as ApcMetaData;
use Phalcon\Mvc\Model\MetaData\Strategy\Annotations as StrategyAnnotations;
use Phalcon\Session\Adapter\Redis as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager;
use Phalcon\MVC\Router;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Core\Tools\MyRedis as Redis;
use Core\Tools\Validator;
use Phalcon\Di;
use Phalcon\Events\Event;
use \Plugins\NotFoundPlugin;
use Plugins\RequestPlugin;


class ApplicationFactory extends FactoryDefault
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
                $this->setShared(lcfirst(substr($method->name, 10)), $method->getClosure($this));
                continue;
            }
            if ((strlen($method->name) > 4) && (strpos($method->name, 'init') === 0)) {
                $this->set(lcfirst(substr($method->name, 4)), $method->getClosure($this));
            }
        }
    }


    /**
     * We register the events manager
     */
    protected function initDispatcher()
    {
        $eventsManager = new EventsManager;
        /**
         * Handle exceptions and not-found exceptions using NotFoundPlugin
         */
        $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);
        $dispatcher = new Dispatcher;
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    }


    /**
     * The URL component is used to generate all kind of urls in the application
     */
    protected function initUrl()
    {
        $url = new UrlProvider();
        $url->setBaseUri($this->get('config')->application->baseUri);
        return $url;
    }

    protected function initView()
    {
//        $config = $this->get('config');
//        $view->registerEngines(array(
//            '.volt' => function ($view, $di) use ($config) {
//                $volt = new VoltEngine($view, $di);
//                $volt->setOptions(array(
//                    'compiledPath' => $config->application->cacheDir,
//                    'compiledSeparator' => '_'
//                ));
//                return $volt;
//            },
//            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
//        ));


        $eventsManager = new EventsManager();

        $eventsManager->attach(
            "view:notFoundView",
            function (Event $event, $view) {
                echo $event->getType();
            }
        );

        $view = new View();
        $view->setViewsDir(VIEWS_PATH);
        $view->registerEngines(array(
            '.volt' => 'volt'
        ));

        // Bind the eventsManager to the view component
        $view->setEventsManager($eventsManager);

        return $view;
    }

    /**
     * Setting up volt
     */
    protected function initSharedVolt($view, $di)
    {
        $volt = new VoltEngine($view, $di);
        $volt->setOptions(array(
            'compiledPath' => COMPILED_PATH,
            'compiledSeparator' => '_'
        ));


        $compiler = $volt->getCompiler();
        $compiler->addFunction('is_a', 'is_a');

        //自定义过滤器
        $compiler->addFilter('cut_str', function($resolvedArgs, $exprArgs) {
            return 'substr(' . $resolvedArgs . ')';
        });

        return $volt;
    }

//    protected function initLog()
//    {
//        $log = new \Log\Log();
//        $log->setBasePath(LOG_PATH . 'seas_log');
//        return $log;
//    }

    protected function initProfiler()
    {
        return new Profiler();
    }


    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    protected function initSharedDb()
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

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    protected function initSharedDbSlave()
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

    protected function initSharedRedis()
    {
        $config = $this->get('config')->get('redis')->toArray();
        $frontCache = new FrontendData(["lifetime" => 172800]);
        $cache = new Redis($frontCache, $config);
        return $cache;
    }

    protected function initRouter()
    {
        $router = new Router();
        $router->add(
            "/",
            [
                "namespace" => "\\Controllers\\Admin",
                "controller" => 'Manager',
                "action" => 'index',
            ]
        );
        $router->add(
            "/:controller/:action/:params",
            [
                "namespace" => "Controllers",
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/debug/:controller/:action/:params",
            [
                "namespace" => "\\Controllers\\Debug",
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/pub/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Pub',
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/admin/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Admin',
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/public/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Pub',
                "controller" => 1,
                "action" => 2,
            ]
        );

        $router->add(
            "/priv/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Priv',
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/private/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Priv',
                "controller" => 1,
                "action" => 2,
            ]
        );
        $router->add(
            "/local/:controller/:action/:params",
            [
                "namespace" => '\\Controllers\\Local',
                "controller" => 1,
                "action" => 2,
            ]
        );

        $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
        return $router;
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
     * Start the session the first time some component request the session service
     */
    protected function initSharedSession()
    {
        $config = $this->get('config')->get('redisSessionAdapter')->toArray();
        $session = new SessionAdapter($config);
        $session->start();
        return $session;
    }

    /**
     * Factory the flash service with custom CSS classes
     */
    protected function initFlash()
    {
        return new FlashSession(array(
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info',
            'warning' => 'alert alert-warning'
        ));
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

    protected function initSharedInput()
    {
        $input = [];
        $request = Di::getDefault()->get('request');
        $jsonpData = $request->get('_json');
        if ($jsonpData) {
            return json_decode($jsonpData, JSON_UNESCAPED_UNICODE);
        }
        if ($request->isGet()) {
            $input = $request->get();
        } else if ($request->isPost()) {
            if (is_array($request->getJsonRawBody(TRUE))) {
                $input = array_merge($request->get(), $request->getJsonRawBody(TRUE));
            } else {
                $input = $request->get();
            }
        } else if ($request->isDelete()) {
            $input = $request->getJsonRawBody(TRUE);
        }
        return $input;
    }

}