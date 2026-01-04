<?php

namespace App\Service\License;

use App\Service\RedisService;
use Exception;
use Psr\Log\LoggerInterface;

readonly class TcgdexRedisService
{
    private const int SETS_TTL = 604800;      // 7 days
    private const int CARDS_TTL = 2592000;    // 30 days
    private const int CARD_TTL = 604800;      // 7 days

    public function __construct(
        private RedisService    $redisService,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * Get sets from cache or API
     * @throws Exception
     */
    public function getExtensions(string $license, mixed $licenseService): mixed
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_sets_{$license}_{$language}";

        $extensions = $this->redisService->getRedis($cacheKey);

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

            $this->redisService->storeRedis($cacheKey, $extensions, self::SETS_TTL);

            return $extensions;
        } catch (Exception $e) {
            $this->logger->error("Error while fetching extensions", [
                "exception" => $e,
            ]);

            throw new Exception("Error when getting sets", previous: $e);
        }
    }

    /**
     * Get cards from a set
     * @throws Exception
     */
    public function getCards(string $license, string $setSelected, mixed $licenseService): array
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_cards_{$license}_{$setSelected}_{$language}";

        $cachedData = $this->redisService->getRedis($cacheKey);

        if ($cachedData) {
            $this->logger->info("Cards loaded from Redis", [
                "cache_key" => $cacheKey,
            ]);

            return $cachedData;
        }

        try {
            [$currentSet, $cards] = $licenseService->handlePokemonSetSelection($setSelected);

            $this->redisService->storeRedis(
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

            throw new Exception("Error when getting cards", previous: $e);
        }
    }

    /**
     * Get a single card by ID
     * @throws Exception
     */
    public function getCardById(string $license, string $cardId, mixed $licenseService): ?array
    {
        $language = $licenseService->getCardsLanguage();
        $cacheKey = "api_card_{$license}_{$cardId}_{$language}";

        // Check cache first
        $cachedCard = $this->redisService->getRedis($cacheKey);
        if ($cachedCard) {
            $this->logger->info("Card loaded from Redis", [
                "cache_key" => $cacheKey,
            ]);
            return $cachedCard;
        }

        try {
            // Get card from license service
            $card = $licenseService->getCardById($cardId);

            if (!$card) {
                return null;
            }

            // Store in cache (ignore cache errors)
            try {
                $this->redisService->storeRedis($cacheKey, $card, self::CARD_TTL);
            } catch (Exception $e) {
                $this->logger->warning("Failed to cache card", [
                    "card_id" => $cardId,
                    "exception" => $e,
                ]);
            }

            return $card;
        } catch (Exception $e) {
            throw new Exception("Error when getting card: " . $e->getMessage(), previous: $e);
        }
    }

    /**
     * Clear license cache
     */
    public function clearLicenseCache(string $license): void
    {
        try {
            // Need to clear all related keys with proper patterns
            $patterns = [
                "api_sets_{$license}_*",
                "api_cards_{$license}_*",
                "api_card_{$license}_*",
            ];

            foreach ($patterns as $pattern) {
                $this->redisService->deleteRedis($pattern);
            }

            $this->logger->info("License cache cleared", [
                "license" => $license,
            ]);
        } catch (Exception $e) {
            $this->logger->error("Failed to clear license cache", [
                "license" => $license,
                "exception" => $e,
            ]);
        }
    }
}
