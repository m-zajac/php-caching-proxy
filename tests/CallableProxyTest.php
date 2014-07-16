<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy;

class CallableProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new Proxy\CallableProxy();
        $this->proxy->setBackend(new Proxy\Backend\Memory());
        $this->proxy->setKeyGenerator(new Proxy\KeyGenerator\Serialize());
        $this->proxy->setSerializer(new Proxy\Serializer\Null());
        $this->proxy->setCacheKey('test_key');
    }

    public function testProxy()
    {
        $this->proxy->setTimeout(1);

        $test_number = 1;
        $callback = function() use (&$test_number) {
            return $test_number++;
        };
        $this->proxy->setCallable($callback);

        $proxy = $this->proxy;  // $this->proxy() won't work
        $result = $proxy();
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        // second call should get cached result, $test_number won't increment
        $result = $proxy();
        $this->assertEquals($result, 1, 'Proxy returned cached data');

        $result = $proxy('arg1');
        $this->assertEquals($result, 2, 'Proxy returned fresh data for second arguments set');

        $result = $proxy('arg1');
        $this->assertEquals($result, 2, 'Proxy returned cached data for second arguments set');

        sleep(2);

        $result = $proxy();
        $this->assertEquals($result, 3, 'Proxy returned fresh data after timeout');
    }
}
