<?php
/**
 * Business 业务逻辑demo
 */

use phpunit\Framework\TestCase;

require_once dirname(__DIR__) . '/app/Business/DemoBusiness.php';


class demoBusinessTest extends TestCase
{
    /**
     * @cover DemoBusiness::sayHello
     */
    public function testSayHello()
    {
        $param = '不告诉你';
        $class = new \Business\DemoBusiness();
        $rs = $class->sayHello($param);;
        $this->assertEquals($rs, 'Hello 不告诉你');
    }

    /**
     * @cover DemoBusiness::testStr
     */
    public function testStr(){
        $param = '不告诉你';
        $class = new \Business\DemoBusiness();
        $rs = $class->testStr($param);;
        $this->assertEquals($rs, 2);
    }

}
?>
