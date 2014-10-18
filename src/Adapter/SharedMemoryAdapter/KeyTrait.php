<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

use AdammBalogh\KeyValueStore\Adapter\Util;
use AdammBalogh\KeyValueStore\Exception\KeyNotFoundException;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait KeyTrait
{
    /**
     * Removes a key.
     *
     * @param string $key
     *
     * @return bool True if the deletion was successful, false if the deletion was unsuccessful.
     *
     * @throws \Exception
     */
    public function delete($key)
    {
        try {
            $this->get($key);
        } catch (KeyNotFoundException $e) {
            return false;
        }

        if (!$this->shmProxy->remove($this->client, $key)) {
            throw new \Exception('Shm remove error');
        }

        return true;
    }

    /**
     * Sets a key's time to live in seconds.
     *
     * @param string $key
     * @param int $seconds
     *
     * @return bool True if the timeout was set, false if the timeout could not be set.
     */
    public function expire($key, $seconds)
    {
        try {
            $value = $this->get($key);
        } catch (KeyNotFoundException $e) {
            return false;
        }

        return $this->set($key, Util::getDataWithExpire($value, $seconds, time()));
    }

    /**
     * Returns the remaining time to live of a key that has a timeout.
     *
     * @param string $key
     *
     * @return int Ttl in seconds.
     *
     * @throws KeyNotFoundException
     * @throws \Exception
     */
    public function getTtl($key)
    {
        if (!$this->shmProxy->has($this->client, $key)) {
            throw new KeyNotFoundException();
        }

        $getResult = $this->shmProxy->get($this->client, $key);
        $unserialized = @unserialize($getResult);

        if (!Util::hasInternalExpireTime($unserialized)) {
            throw new \Exception('Cannot retrieve ttl');
        }

        return $this->handleTtl($key, $unserialized['ts'], $unserialized['s']);
    }

    /**
     * Determines if a key exists.
     *
     * @param string $key
     *
     * @return bool True if the key does exist, false if the key does not exist.
     */
    public function has($key)
    {
        try {
            $this->get($key);
        } catch (KeyNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Removes the existing timeout on key, turning the key from volatile (a key with an expire set)
     * to persistent (a key that will never expire as no timeout is associated).
     *
     * @param string $key
     *
     * @return bool True if the persist was success, false if the persis was unsuccessful.
     *
     * @throws \Exception
     */
    public function persist($key)
    {
        if (!$this->shmProxy->has($this->client, $key)) {
            return false;
        }

        $getResult = $this->shmProxy->get($this->client, $key);
        $unserialized = @unserialize($getResult);

        if (!Util::hasInternalExpireTime($unserialized)) {
            throw new \Exception("{$key} has no associated timeout");
        }

        try {
            $this->handleTtl($key, $unserialized['ts'], $unserialized['s']);
        } catch (KeyNotFoundException $e) {
            return false;
        }

        return $this->set($key, $unserialized['v']);
    }
}
