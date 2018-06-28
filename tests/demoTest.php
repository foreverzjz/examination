<?php
/**
 * 常用例子demo
 */
use phpunit\Framework\TestCase;
  
class demoTest extends TestCase
{
    public function testTrue()
    {
        //断言正确
        $this->assertTrue(true);
    }

    public function testPushAndPop()
    {  
        $stack = [];
        //相等
        $this->assertEquals(0, count($stack));  
        array_push($stack, 'foo');  
        $this->assertEquals('foo', $stack[count($stack) - 1]);  
        $this->assertEquals(1, count($stack));  
        $this->assertEquals('foo', array_pop($stack));  
        $this->assertEquals(0, count($stack));
        //空判断
        $this->assertEmpty($stack);
    }

    public function testArray()
    {
        //数组有某个key
        $this->assertArrayHasKey('bar', array('bar' => 'baz'));
        //数组没有某个key
        $this->assertArrayNotHasKey('foo', array('bar' => 'baz'));
    }

}
?>
