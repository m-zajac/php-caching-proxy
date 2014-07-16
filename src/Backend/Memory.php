<?php

namespace MZ\Proxy\Backend;

class Memory implements BackendInterface
{
    protected $storage = array();

    public function set($key, $value, $timeout = 0)
    {
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

    public function clear($key)
    {
        unset($this->storage[$key]);

        return $this;
    }

    public function get($key)
    {
        if (!isset($this->storage[$key])) {
            return null;
        }

        $data = $this->storage[$key];
        if ($data['timeout'] and $data['timeout'] < time()) {
            return null;
        }

        return $data['value'];
    }
}
