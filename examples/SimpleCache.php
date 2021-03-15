<?php declare(strict_types=1);

require_once 'vendor/psr/simple-cache/src/CacheInterface.php';

class SimpleCache implements \Psr\SimpleCache\CacheInterface
{
    /**
     * @var array
     */
    protected $store = [];

    public function get($key, $default = null)
    {
        return $this->store[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->store[$key] = $value;
    }

    public function delete($key)
    {
        unset($this->store[$key]);
    }

    public function clear()
    {
        $this->store = [];
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = $this->get($key);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $value) {
            $this->set($value, $ttl);
        }
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function has($key)
    {
        return isset($this->store[$key]);
    }
}