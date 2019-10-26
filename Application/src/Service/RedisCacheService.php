<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Predis\Client;

class RedisCacheService
{
    const TTL_MINUTE = 60;
    const TTL_HOUR = 3660;
    const TTL_DAY = 86400;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer= $serializer;
    }

    /**
     * @param $key
     * @return array
     * @internal param $object
     */
    public function get($key)
    {
        $this->client->get($key);
    }

    /**
     * @param $key
     * @return array
     */
    public function invalidate($key)
    {
        $this->client->del(array($key));
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     * @return object
     */
    public function set($key, $value, $ttl = 0)
    {
        if($ttl >0) {
            $this->client->setex($key, $ttl, $value);
        }else{
            $this->client->set($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     * @return object
     */
    public function setObject($key, $value, $ttl = 0)
    {
        $json=$this->serializer->serialize($value, 'json', SerializationContext::create()->enableMaxDepthChecks());
        $this->set($key, $json, $ttl);
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     * @return object
     */
    public function getObject($key)
    {
        $object=$this->client->get($key);
        if(!$object){
            return false;
        }
        return $this->serializer->deserialize($object, 'array','json');
    }
}
