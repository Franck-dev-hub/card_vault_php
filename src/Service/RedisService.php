<?php
namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;


readonly class RedisService
{
    /**
     * Store a value in Redis
     * @throws InvalidArgumentException
     */
    public function storeRedis(CacheItemPoolInterface $cache, $key, $value, $time): void
    {
        $item = $cache->getItem($key);
        $item->set($value);
        // 86400 = 24H
        // 604800 = 7days
        // 2592000 = 30 days
        $item->expiresAfter($time);
        $cache->save($item);
    }

    /**
     * Get a value from Redis
     * @throws InvalidArgumentException
     */
    public function getRedis(CacheItemPoolInterface $cache, $key)
    {
        $item = $cache->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        } else {
            return null;
        }
    }

    /**
     * Delete a value in Redis
     * @throws InvalidArgumentException
     */
    public function deleteRedisItem(CacheItemPoolInterface $cache, $key): void
    {
        $cache->deleteItem($key);
    }

    /**
     * check if value exist in Redis
     * @throws InvalidArgumentException
     */
    public function existsRedis(CacheItemPoolInterface $cache, $key): bool
    {
        return $cache->getItem($key)->isHit();
    }
}
