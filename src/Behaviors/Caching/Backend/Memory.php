<?php

namespace MZ\Proxy\Behaviors\Caching\Backend;

class Memory implements BackendInterface
{
    protected $storage = array();

    public function set($namespace, array $arguments, $value, $timeout = 0)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        $timeout_time = null;
        if ($timeout) {
            $timeout_time = time() + (int)$timeout;
        }
        $this->storage[$key] = array(
            'timeout' => $timeout_time,
            'value' => $value,
        );

        return $this;
    }

    public function clear($namespace, array $arguments)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        unset($this->storage[$key]);

        return $this;
    }

    public function get($namespace, array $arguments)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        if (!isset($this->storage[$key])) {
            return null;
        }

        $data = $this->storage[$key];
        if ($data['timeout'] and $data['timeout'] < time()) {
            return null;
        }

        return $data['value'];
    }

    protected function getKeyFromArguments($namespace, array $arguments)
    {
        $key = '';
        foreach ($arguments as $arg) {
            $key .= serialize($arg);
        }

        return $namespace.$key;
    }
}
