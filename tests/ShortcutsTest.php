<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy;
use MZ\Proxy\Shortcuts;

class ShortcutsTest extends \PHPUnit_Framework_TestCase
{
    public function testWraps()
    {
        $data = array();
        $data[] = array(
            'shortcut' => function ($subject) {
                return Shortcuts\wrapWithMemoryProxy($subject);
            },
            'behavior_class' => 'MZ\Proxy\Behaviors\Caching\CachingBehavior'
        );
        if (class_exists('\Memcache')) {
            $data[] = array(
                'shortcut' => function ($subject) {
                    return Shortcuts\wrapWithMemcacheProxy($subject);
                },
                'behavior_class' => 'MZ\Proxy\Behaviors\Caching\CachingBehavior'
            );
        }

        foreach ($data as $test_data) {
            $subject1 = new \stdClass();
            $proxy = $test_data['shortcut']($subject1);
            $this->assertInstanceOf('MZ\Proxy\ObjectProxy', $proxy);

            $subject2 = function () {
                return 'ok';
            };
            $proxy = $test_data['shortcut']($subject2);
            $this->assertInstanceOf('MZ\Proxy\CallableProxy', $proxy);

            $this->assertInstanceOf($test_data['behavior_class'], $proxy->getBehavior());
        }
    }

    /**
     * @@expectedException \InvalidArgumentException
     */
    public function testWrapWithMemoryProxyInvalidInput1()
    {
        $proxy = Shortcuts\wrapWithMemoryProxy('test');
    }

    /**
     * @@expectedException \InvalidArgumentException
     */
    public function testWrapWithMemoryProxyInvalidInput2()
    {
        $proxy = Shortcuts\wrapWithMemoryProxy(123);
    }

    /**
     * @@expectedException \InvalidArgumentException
     */
    public function testWrapWithMemoryProxyInvalidInput3()
    {
        $proxy = Shortcuts\wrapWithMemoryProxy(array(1, 2, 3));
    }
}
