<?php namespace AdammBalogh\KeyValueStore;

use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter;

abstract class AbstractKvsSharedMemoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyValueStore
     */
    protected $kvs;

    /**
     * @var SharedMemoryAdapter
     */
    protected $shmAdapter;

    /**
     * @var string
     */
    protected $tmpFileName;

    /**
     * @var resource
     */
    protected $shmResource;

    public function setUp()
    {
        $this->tmpFileName = tempnam('/tmp', 'KVS');

        $this->shmResource = shm_attach(ftok($this->tmpFileName, 'a'));
        if (!$this->shmResource) {
            die('Unable to create the shared memory segment');
        }

        $this->shmAdapter = new SharedMemoryAdapter($this->shmResource);

        $this->kvs = new KeyValueStore($this->shmAdapter);
    }

    public function tearDown()
    {
        shm_remove($this->shmResource);
        shm_detach($this->shmResource);
        @unlink($this->tmpFileName);
        unset($this->shmAdapter, $this->kvs);
    }
}
