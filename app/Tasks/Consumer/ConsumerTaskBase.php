<?php
/**
 * MQ消费者基础类
 * User: peter
 * Date: 2017/9/7
 * Time: 下午5:22
 * @property \Phalcon\Config $config;
 * @property \Core\Tools\RabbitMQ $_mq;
 *
 */

namespace Tasks\Consumer;

use Phalcon\Cli\Task;
use Core\Tools\RabbitMQ;


/**
 * @property \Phalcon\Config\Adapter\Ini config
 * @property \Core\Tools\RabbitMQ _mq;
 */
class ConsumerTaskBase extends Task
{
    protected $_mq = NULL;

    public function initialize()
    {
        $this->_mq = new RabbitMQ($this->config->rabbitmq->toArray());
    }

}