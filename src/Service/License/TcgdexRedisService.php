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
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_sets_{$license}_{$language}";
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
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_cards_{$license}_{$setSelected}_{$language}";
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
     * Get a single card by ID
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCardById(string $license, string $cardId, mixed $licenseService): ?array
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_card_{$license}_{$cardId}_{$language}";

        // Check cache first
        try {
            $cachedCard = $this->redisService->getRedis($this->cache, $cacheKey);
            if ($cachedCard) {
                return $cachedCard;
            }
        } catch (InvalidArgumentException $e) {
            // Else continue
        }

        try {
            // Get card from license service
            $card = $licenseService->getCardById($cardId);

            if (!$card) {
                return null;
            }

            // Store in cache
            try {
                $this->redisService->storeRedis(
                    $this->cache,
                    $cacheKey,
                    $card,
                    604800 // 7 days
                );
            } catch (InvalidArgumentException $e) {
                // Ignore cache errors
            }

            return $card;
        } catch (Exception $e) {
            throw new Exception("Error when getting card : " . $e->getMessage());
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
