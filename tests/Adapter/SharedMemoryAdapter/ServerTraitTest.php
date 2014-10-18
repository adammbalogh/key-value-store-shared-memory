<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsSharedMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class ServerTraitTest extends AbstractKvsSharedMemoryTestCase
{
    public function testFlush()
    {
        $this->kvs->set('key', 5);

        $this->assertTrue($this->kvs->has('key'));
        $this->assertNull($this->kvs->flush());
    }

    /**
     * @expectedException \Exception
     */
    public function testFlushException()
    {
        $shmProxyStub = \Mockery::mock('\AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ShmProxy');
        $shmProxyStub->shouldReceive('flush')->with($this->shmResource)->andReturn(false);

        $kvs = new KeyValueStore(new SharedMemoryAdapter($this->shmResource, $shmProxyStub));

        $kvs->flush();
    }
}
