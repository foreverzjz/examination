<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/7/12
 * Time: 下午3:30
 */
use phpunit\Framework\TestCase;



class TestBase  extends TestCase
{
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        spl_autoload_register(array('TestBase','autoload'));
    }

    public static function autoload($className){

    }
}