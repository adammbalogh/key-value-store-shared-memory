<?php namespace AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsSharedMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class KeyTraitTest extends AbstractKvsSharedMemoryTestCase
{
    public function testDelete()
    {
        $this->kvs->set('key', 5);

        $this->assertTrue($this->kvs->delete('key'));
    }

    public function testDeleteKeyNotFound()
    {
        $this->assertFalse($this->kvs->delete('key'));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\InternalException
     */
    public function testDeleteError()
    {
        $shmProxyStub = \Mockery::mock('\AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ShmProxy[remove]');
        $shmProxyStub->shouldReceive('remove')->with($this->shmResource, 'key')->andReturn(false);

        $kvs = new KeyValueStore(new SharedMemoryAdapter($this->shmResource, $shmProxyStub));

        $kvs->set('key', 5);
        $kvs->delete('key');
    }

    public function testExpire()
    {
        $this->kvs->set('key', 5);
        $this->assertTrue($this->kvs->expire('key', 10));
    }

    public function testExpireKeyNotFound()
    {
        $this->assertFalse($this->kvs->expire('key', 10));
    }

    public function testGetTtl()
    {
        $this->kvs->set('key', 5);
        $this->kvs->expire('key', 10);
        $this->assertEquals(10, $this->kvs->getTtl('key'));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     */
    public function testGetTtlKeyNotFound()
    {
        $this->kvs->getTtl('key');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetTtlNotSerialized()
    {
        $this->kvs->set('key', 5);
        $this->kvs->getTtl('key');
    }

    public function testHas()
    {
        $this->kvs->set('key', 'value');
        $this->assertTrue($this->kvs->has('key'));

        $this->kvs->delete('key');

        $this->assertFalse($this->kvs->has('key'));
    }

    public function testPersist()
    {
        $this->kvs->set('key', 'value');
        $this->kvs->expire('key', 3);

        $this->assertTrue($this->kvs->persist('key'));
    }

    public function testPersistKeyNotFound()
    {
        $this->assertFalse($this->kvs->persist('key'));
    }
    
    public function testPersistError()
    {
        $this->kvs->set('key', 'value');

        $this->assertFalse($this->kvs->persist('key'));
    }

    public function testPersistExpired()
    {
        $this->kvs->set('key', 'value');
        $this->kvs->expire('key', 0);
        //sleep(1);

        $this->assertFalse($this->kvs->persist('key'));
    }
}
