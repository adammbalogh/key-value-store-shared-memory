<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsSharedMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class ValueTraitTest extends AbstractKvsSharedMemoryTestCase
{
    public function testGet()
    {
        $this->kvs->set('key', 51);

        $this->assertEquals(51, $this->kvs->get('key'));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     */
    public function testGetKeyNotFound()
    {
        $this->kvs->get('key');
    }

    public function testGetSerialized()
    {
        $this->kvs->set('key', 101);
        $this->kvs->expire('key', 5);

        $this->assertEquals(101, $this->kvs->get('key'));
    }

    public function testSet()
    {
        $this->assertTrue($this->kvs->set('key', 6));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\InternalException
     */
    public function testSetError()
    {
        $shmProxyStub = \Mockery::mock('\AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ShmProxy');
        $shmProxyStub->shouldReceive('put')->with($this->shmResource, 'key', 'value')->andReturn(false);

        $kvs = new KeyValueStore(new SharedMemoryAdapter($this->shmResource, $shmProxyStub));

        $kvs->set('key', 'value');
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     */
    public function testHandleTtlKeyNotFound()
    {
        $this->kvs->set('key', 'value');
        $this->kvs->expire('key', 0);
        sleep(2);
        $this->kvs->get('key');
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\InternalException
     */
    public function testHandleTtlError()
    {
        $shmProxyStub = \Mockery::mock('\AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ShmProxy[remove]');
        $shmProxyStub->shouldReceive('remove')->andReturn(false);

        $kvs = new KeyValueStore(new SharedMemoryAdapter($this->shmResource, $shmProxyStub));

        $kvs->set('key', 'value');
        $kvs->expire('key', 0);
        sleep(1);
        $kvs->get('key');
    }
}
