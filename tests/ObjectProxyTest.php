<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy\ObjectProxy;
use MZ\Proxy\Behaviors;

class ObjectProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new ObjectProxy();
    }

    public function testObjectAccess()
    {
        $object = new \stdClass();
        $object->test = 123;

        $this->proxy->proxySetObject($object);

        $this->assertTrue($object === $this->proxy->proxyGetObject());
    }

    public function testMethodsConfig()
    {
        $this->proxy->proxySetMethods('test');
        $this->assertEquals($this->proxy->proxyGetMethods(), array('test'));

        $this->proxy->proxySetMethods(array('test1', 'test2'));
        $this->assertEquals($this->proxy->proxyGetMethods(), array('test1', 'test2'));

        $this->proxy->proxySetMethods(null);
        $this->assertEquals($this->proxy->proxyGetMethods(), null);
    }

    public function testBehaviorAccess()
    {
        $behavior = new Behaviors\Caching\CachingBehavior();
        $this->proxy->proxySetBehavior($behavior);

        $this->assertEquals($this->proxy->proxyGetBehavior(), $behavior);
    }
}
