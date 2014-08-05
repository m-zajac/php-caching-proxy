<?php

namespace MZ\Proxy\Tests\LazyInit;

use MZ\Proxy\CallableProxy;
use MZ\Proxy\Behaviors\LazyInit;

class CallableProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new CallableProxy();

        $behavior = new LazyInit\LazyInitBehavior();
        $this->proxy->setBehavior($behavior);
    }

    public function testProxy()
    {
        $test_number = 1;
        $this->proxy->getBehavior()->setLoader(function () use (&$test_number) {
            return function () use (&$test_number) {
                return $test_number++;
            };
        });

        $proxy = $this->proxy;  // $this->proxy() won't work
        $this->assertNull($proxy->getCallable());  // no subject before first call

        $result = $proxy();
        $this->assertEquals($result, 1, 'Proxy returned valid data');
        $this->assertNotNull($proxy->getCallable());  // callable instantiated

        $result = $proxy('arg1');
        $this->assertEquals($result, 2, 'Proxy returned valid data');

        $result = $proxy('arg2');
        $this->assertEquals($result, 3, 'Proxy returned valid data');
    }
}
