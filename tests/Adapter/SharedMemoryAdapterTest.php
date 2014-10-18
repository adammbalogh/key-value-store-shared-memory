<?php namespace AdammBalogh\KeyValueStore\Adapter;

use AdammBalogh\KeyValueStore\AbstractKvsSharedMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

class SharedMemoryAdapterTest extends AbstractKvsSharedMemoryTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstansiationError()
    {
        new SharedMemoryAdapter(null);
    }

    public function testGetClient()
    {
        $this->assertEquals($this->shmResource, $this->shmAdapter->getClient());
    }
}
