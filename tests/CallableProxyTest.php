<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy\CallableProxy;

class CallableProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new CallableProxy();
    }

    public function testProxy()
    {
        $callback = function () {
            return 'ok';
        };
        $this->proxy->setCallable($callback);

        $this->assertTrue($callback === $this->proxy->getCallable());
    }
}
