<?php

namespace App\Service\License;

use App\Service\RedisService;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class TcgdexRedisService
{
    public function __construct(
        private RedisService           $redisService,
        private CacheItemPoolInterface $cache,
    ) {}

    /**
     * Get sets from cache or API
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getExtensions(string $license, mixed $licenseService): mixed
    {
        $cacheKey = "api_sets_{$license}";
        $extensions = $this->redisService->getRedis($this->cache, $cacheKey);

        if ($extensions) {
            return $extensions;
        }

        try {
            $extensions = $licenseService->fetchPokemonSets();
            $this->redisService->storeRedis($this->cache, $cacheKey, $extensions, 604800);
            return $extensions;
        } catch (Exception $e) {
            throw new Exception("Error when getting set : " . $e->getMessage());
        }
    }

    /**
     * Get cards from a set
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCards(string $license, string $setSelected, mixed $licenseService): array
    {
        $cacheKey = "api_cards_{$license}_{$setSelected}";
        $cachedData = $this->redisService->getRedis($this->cache, $cacheKey);

        if ($cachedData) {
            return $cachedData;
        }

        try {
            [$currentSet, $cards] = $licenseService->handlePokemonSetSelection($setSelected);

            $this->redisService->storeRedis(
                $this->cache,
                $cacheKey,
                [$currentSet, $cards],
                2592000
            );

            return [$currentSet, $cards];
        } catch (Exception $e) {
            throw new Exception("Error when getting cards : " . $e->getMessage());
        }
    }

    /**
     * Clear license cache
     * @throws InvalidArgumentException
     */
    public function clearLicenseCache(string $license): void
    {
        $this->redisService->deleteRedisItem($this->cache, "api_sets_{$license}");
    }
}
