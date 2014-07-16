<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy\Backend as Backends;

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
    }

    public function testSetGet()
    {
        $test_key = 'as sdasasdf';
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend) {
            $backend->set($test_key, $test_value);
            $this->assertEquals(
                $backend->get($test_key),
                $test_value,
                'Returned valid data'
            );
        }
    }

    public function testUnset()
    {
        $test_key = 'as sdasasdf';
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend) {
            $backend->clear($test_key);
            $this->assertEquals(
                $backend->get($test_key),
                null,
                'Data deleted properly'
            );
        }
    }

    public function testTimeout()
    {
        $test_key = 'as sdasasdf';
        $test_value = 'asdsfsad fsadfsadf sadfsadf';

        foreach ($this->backends as $backend) {
            $backend->set($test_key, $test_value, 1);
            $this->assertEquals(
                $backend->get($test_key),
                $test_value
            );
            sleep(2);
            $this->assertEquals(
                $backend->get($test_key),
                null,
                'Data expired propelry'
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
