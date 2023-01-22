<?php

use PHPUnit\Framework\TestCase;

/**
 * 单元测试
 */
class HelpersTest extends TestCase
{
    /**
     * 测试获取子孙树
     * User: Clannad ~ ☆
     */
    public function testGetTree()
    {
        // todo 完善
    }

    /**
     * 验证数字大小对比
     * User: Clannad ~ ☆
     */
    public function testBcCompNumber()
    {
        $result = bcCompNumber(1,'<',2);
        $this->assertTrue($result);
    }

    /**
     * 验证格式化电话号码
     * User: Clannad ~ ☆
     */
    public function testCutTel()
    {
        $tel = '13500004567';
        $result = cutTel($tel);
        $this->assertEquals('135****4567',$result);
    }
}