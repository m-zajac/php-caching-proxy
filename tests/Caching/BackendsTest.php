<?php

namespace MZ\Proxy\Tests\Caching;

use MZ\Proxy\Behaviors\Caching\Backend as Backends;

class BackendsTest extends \PHPUnit_Framework_TestCase
{
    protected $backends;

    public function setUp()
    {
        $this->backends = array();
        $this->backends['memory'] = new Backends\Memory;
        if (class_exists('\Memcache')) {
            $backend = new Backends\Memcache('prefix');
            $backend->addServer('localhost', 11211);
            $this->assertEquals(
                $backend->getServerStatus('localhost'),
                1,
                'Memcache serwer ok'
            );
            $this->backends['memcache'] = $backend;
        } else {
            print_r("No Memcache class\n");
        }

        $this->test_arguments = array(
            'as sdasasdf',
            new \stdClass(),
            true,
            123
        );
        $this->namespace = 'test';
    }

    public function testSetGet()
    {
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend_name => $backend) {
            $backend->clear($this->namespace, $this->test_arguments);
            $this->assertNull(
                $backend->get($this->namespace, $this->test_arguments),
                "$backend_name - returned null when no data"
            );
            $backend->set($this->namespace, $this->test_arguments, $test_value);
            $this->assertEquals(
                $backend->get($this->namespace, $this->test_arguments),
                $test_value,
                "$backend_name - returned valid data"
            );
        }
    }

    public function testUnset()
    {
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend_name => $backend) {
            $backend->clear($this->namespace, $this->test_arguments);
            $this->assertEquals(
                $backend->get($this->namespace, $this->test_arguments),
                null,
                "$backend_name - data deleted properly"
            );
        }
    }

    public function testTimeout()
    {
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend_name => $backend) {
            $backend->set($this->namespace, $this->test_arguments, $test_value, 1);
            $this->assertEquals(
                $backend->get($this->namespace, $this->test_arguments),
                $test_value,
                "$backend_name - returned valid data"
            );
            sleep(2);
            $this->assertEquals(
                $backend->get($this->namespace, $this->test_arguments),
                null,
                "$backend_name - data expired propelry"
            );
        }
    }

    public function testMemcacheBackend()
    {
        if (!class_exists('\Memcache')) {
            $this->markTestSkipped('No Memcache class, skipping...');

            return;
        }

        $memcache_mock = $this->getMock('\Memcache', array());

        $backend = new Backends\Memcache('prefix', $memcache_mock);

        $memcache_mock
            ->expects($this->once())
            ->method('addServer')
            ->with('localhost', 11211)
        ;
        $backend->addServer('localhost', 11211);
    }
}
