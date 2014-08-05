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
            'behavior_class' => 'MZ\Proxy\Behaviors\Caching\CachingBehavior',
            'subject_types' => array('object', 'callable'),
        );
        if (class_exists('\Memcache')) {
            $data[] = array(
                'shortcut' => function ($subject) {
                    return Shortcuts\wrapWithMemcacheProxy($subject);
                },
                'behavior_class' => 'MZ\Proxy\Behaviors\Caching\CachingBehavior',
                'subject_types' => array('object', 'callable'),
            );
        }
        $data[] = array(
            'shortcut' => function ($subject) {
                return Shortcuts\createLazyInitProxy(function () use ($subject) {
                    return $subject;
                });
            },
            'behavior_class' => 'MZ\Proxy\Behaviors\LazyInit\LazyInitBehavior',
            'subject_types' => array('object'),
        );

        foreach ($data as $test_data) {
            if (in_array('object', $test_data['subject_types'])) {
                $subject = new \stdClass();
                $proxy = $test_data['shortcut']($subject);
                $this->assertInstanceOf('MZ\Proxy\ObjectProxy', $proxy);
                $this->assertInstanceOf($test_data['behavior_class'], $proxy->proxyGetBehavior());
            }

            if (in_array('callable', $test_data['subject_types'])) {
                $subject = function () {
                    return 'ok';
                };
                $proxy = $test_data['shortcut']($subject);
                $this->assertInstanceOf('MZ\Proxy\CallableProxy', $proxy);
                $this->assertInstanceOf($test_data['behavior_class'], $proxy->getBehavior());
            }
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
