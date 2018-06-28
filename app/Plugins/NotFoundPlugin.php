<?php

namespace Plugins;

use Phalcon\Di;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class NotFoundPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param MvcDispatcher $dispatcher
     * @param Exception $exception
     * @return boolean
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, $exception)
    {

        if ($exception instanceof DispatcherException) {
            $this->response->setStatusCode(404, "Not Found");
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    $this->response->setContent('Handler doesn\'t exist.');
                    break;
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $this->response->setContent('Action doesn\'t exist.');
                    break;
            }
        }else{
            if(Di::getDefault()->get('config')->debug->app){
                $errorContent = '<pre>FILE:'.$exception->getFile().PHP_EOL;
                $errorContent .= 'CODE:'.$exception->getCode().PHP_EOL;
                $errorContent .= 'LINE:'.$exception->getLine().PHP_EOL;
                $errorContent .= 'MSG:'.$exception->getMessage().PHP_EOL;
                $errorContent .= 'TRACE:'.$exception->getTraceAsString().'</pre>';
            }
            $this->response->setContent($errorContent);
        }
        $this->response->send();
        exit();
    }
}
