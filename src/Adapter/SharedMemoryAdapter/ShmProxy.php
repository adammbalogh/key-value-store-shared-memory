<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

class ShmProxy
{
    /**
     * @param resource $shmIdentifier
     * @param string $variableKey
     *
     * @return mixed
     */
    public function get($shmIdentifier, $variableKey)
    {
        return shm_get_var($shmIdentifier, $this->keyToShmIntKey($variableKey));
    }

    /**
     * @param resource $shmIdentifier
     * @param string $variableKey
     * @param mixed $variable
     *
     * @return bool
     */
    public function put($shmIdentifier, $variableKey, $variable)
    {
        return @shm_put_var($shmIdentifier, $this->keyToShmIntKey($variableKey), $variable);
    }

    /**
     * @param resource$shmIdentifier
     * @param string $variableKey
     *
     * @return bool
     */
    public function remove($shmIdentifier, $variableKey)
    {
        return shm_remove_var($shmIdentifier, $this->keyToShmIntKey($variableKey));
    }

    /**
     * @param resource$shmIdentifier
     * @param string $variableKey
     *
     * @return bool
     */
    public function has($shmIdentifier, $variableKey)
    {
        return shm_has_var($shmIdentifier, $this->keyToShmIntKey($variableKey));
    }

    /**
     * @param resource $shmIdentifier
     *
     * @return bool
     */
    public function flush($shmIdentifier)
    {
        return shm_remove($shmIdentifier);
    }

    /**
     * @param string $key
     *
     * @return int
     */
    protected function keyToShmIntKey($key)
    {
        return (int)sprintf("%u\n", crc32($key));
    }
}
