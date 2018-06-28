<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/7/12
 * Time: 下午1:38
 */

class agentTest extends TestBase
{
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

    }

    public function addToAssertionCount($count)
    {

    }

    public function testPackageParam(){
        $param = ['uid' => 12];
echo BASE_PATH;
        $class = new \Controllers\AgentController();
        $rs = $class->packageParam($param);
        $this->assertArrayHasKey('target', $rs);
        $this->assertArrayHasKey('public', $rs);
        $this->assertArraySubset($rs,['target'=>[],'public'=>'']);

    }
}