<?php namespace AdammBalogh\KeyValueStore\Adapter;

use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ShmProxy;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\KeyTrait;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ValueTrait;
use AdammBalogh\KeyValueStore\Adapter\SharedMemoryAdapter\ServerTrait;

class SharedMemoryAdapter extends AbstractAdapter
{
    use KeyTrait, ValueTrait, ServerTrait;

    /**
     * @var resource
     */
    protected $client;

    /**
     * @var ShmProxy
     */
    protected $shmProxy;

    /**
     * @param resource $client
     * @param ShmProxy $shmProxy
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($client, ShmProxy $shmProxy = null)
    {
        if (!is_resource($client)) {
            throw new \InvalidArgumentException();
        }

        if (is_null($shmProxy)) {
            $this->shmProxy = new ShmProxy();
        } else {
            $this->shmProxy = $shmProxy;
        }

        $this->client = $client;
    }

    /**
     * @return resource
     */
    public function getClient()
    {
        return $this->client;
    }
}
