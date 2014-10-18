<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

use AdammBalogh\KeyValueStore\Adapter\Util;
use AdammBalogh\KeyValueStore\Exception\KeyNotFoundException;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait ValueTrait
{
    /**
     * Gets the value of a key.
     *
     * @param string $key
     *
     * @return mixed The value of the key.
     *
     * @throws KeyNotFoundException
     */
    public function get($key)
    {
        return $this->getValue($key);
    }

    /**
     * Sets the value of a key.
     *
     * @param string $key
     * @param mixed $value Can be any of serializable data type.
     *
     * @return bool True if the set was successful, false if it was unsuccessful.
     *
     * @throws \Exception
     */
    public function set($key, $value)
    {
        if (!$this->shmProxy->put($this->client, $key, $value)) {
            throw new \Exception('Shm put error');
        }

        return true;
    }

    /**
     * Gets value, watches expiring.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws KeyNotFoundException
     */
    protected function getValue($key)
    {
        if (!$this->shmProxy->has($this->client, $key)) {
            throw new KeyNotFoundException();
        }

        $getResult = $this->shmProxy->get($this->client, $key);
        $unserialized = @unserialize($getResult);

        if (Util::hasInternalExpireTime($unserialized)) {
            $this->handleTtl($key, $unserialized['ts'], $unserialized['s']);

            $getResult = $unserialized['v'];
        }

        return $getResult;
    }

    /**
     * If ttl is lesser or equals 0 delete key.
     *
     * @param string $key
     * @param int $expireSetTs
     * @param int $expireSec
     *
     * @return int ttl
     *
     * @throws KeyNotFoundException
     * @throws \Exception
     */
    protected function handleTtl($key, $expireSetTs, $expireSec)
    {
        $ttl = $expireSetTs + $expireSec - time();
        if ($ttl <= 0) {
            if (!$this->shmProxy->remove($this->client, $key)) {
                throw new \Exception('Shm remove error');
            }

            throw new KeyNotFoundException();
        }

        return $ttl;
    }
}
