<?php

namespace App\Service\License;

use App\Service\RedisService;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

readonly class TcgdexRedisService
{
    private const int SETS_TTL  = 604800;
    private const int CARDS_TTL = 2592000;

    public function __construct(
        private RedisService           $redisService,
        private CacheItemPoolInterface $cache,
        private LoggerInterface        $logger,
    ) {}

    /**
     * Get sets from cache or API
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getExtensions(string $license, mixed $licenseService): mixed
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_sets_{$license}_{$language}";

        $extensions = $this->redisService->getRedis($this->cache, $cacheKey);

        if ($extensions) {
            $this->logger->info("Extension loaded from Redis", [
                "cache_key" => $cacheKey,
            ]);
            return $extensions;
        }

        try {
            $this->logger->info("Extension NOT loaded from Redis", [
                "cache_key" => $cacheKey,
            ]);

            $extensions = $licenseService->fetchPokemonSets();

            $this->redisService->storeRedis(
                $this->cache,
                $cacheKey,
                $extensions,
                self::SETS_TTL
            );

            return $extensions;
        } catch (Exception $e) {
            $this->logger->error("Error while fetching extensions", [
                "exception" => $e,
            ]);

            throw new Exception(
                "Error when getting sets",
                previous: $e
            );
        }
    }

    /**
     * Get cards from a set
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCards(string $license, string $setSelected, mixed $licenseService): array
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_cards_{$license}_{$setSelected}_{$language}";

        $cachedData = $this->redisService->getRedis($this->cache, $cacheKey);

        if ($cachedData) {
            $this->logger->info("Cards loaded from Redis", [
                "cache_key" => $cacheKey,
            ]);

            return $cachedData;
        }

        try {
            [$currentSet, $cards] = $licenseService->handlePokemonSetSelection($setSelected);

            $this->redisService->storeRedis(
                $this->cache,
                $cacheKey,
                [$currentSet, $cards],
                self::CARDS_TTL
            );

            return [$currentSet, $cards];
        } catch (Exception $e) {
            $this->logger->error("Error while fetching cards", [
                "set" => $setSelected,
                "exception" => $e,
            ]);

            throw new Exception(
                "Error when getting cards",
                previous: $e
            );
        }
    }

    /**
     * Clear license cache
     * @throws InvalidArgumentException
     */
    public function clearLicenseCache(string $license): void
    {
        $this->redisService->deleteRedisItem($this->cache, "api_sets_{$license}");
        $this->logger->info("License cache cleared", [
            "license" => $license,
        ]);
    }
}
