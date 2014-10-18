<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

trait ServerTrait
{
    /**
     * Removes all keys.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function flush()
    {
        if (!$this->shmProxy->flush($this->client)) {
            throw new \Exception('Shm remove error');
        }
    }
}
