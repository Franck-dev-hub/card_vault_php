<?php
namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;

readonly class RedisService
{
    private const int EXPIRY_24H = 86400;
    private const int EXPIRY_7D = 604800;
    private const int EXPIRY_30D = 2592000;

    public function __construct(
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger,
    ) {}

    /**
     * Store a value in Redis
     */
    public function storeRedis(string $key, mixed $value, int $expiryTime = self::EXPIRY_24H): void
    {
        try {
            $item = $this->cache->getItem($key);
            $item->set($value);
            $item->expiresAfter($expiryTime);
            $this->cache->save($item);

            $this->logger->debug("Redis key stored", ["key" => $key, "expiry" => $expiryTime]);
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Failed to store Redis key", ["key" => $key, "error" => $e->getMessage()]);
            throw new RuntimeException("Failed to store key '{$key}' in Redis", 0, $e);
        }
    }

    /**
     * Get a value from Redis
     */
    public function getRedis(string $key): mixed
    {
        try {
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                $this->logger->debug("Redis key hit", ["key" => $key]);
                return $item->get();
            }
            $this->logger->debug("Redis key miss", ["key" => $key]);
            return null;
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Failed to retrieve Redis key", ["key" => $key, "error" => $e->getMessage()]);
            throw new RuntimeException("Failed to retrieve key '{$key}' from Redis", 0, $e);
        }
    }

    /**
     * Delete a specific key from Redis
     */
    public function deleteRedis(string $key): void
    {
        try {
            $this->cache->deleteItem($key);
            $this->logger->debug("Redis key deleted", ["key" => $key]);
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Failed to delete Redis key", ["key" => $key, "error" => $e->getMessage()]);
            throw new RuntimeException("Failed to delete key '{$key}' from Redis", 0, $e);
        }
    }

    /**
     * Delete keys matching a pattern (for wildcard deletions)
     */
    public function deleteByPattern(string $pattern): int
    {
        try {
            // If using Redis directly, implement SCAN + DEL
            // For now, this is a placeholder - you may need native Redis access
            $this->logger->warning("Pattern deletion requested but not fully implemented", ["pattern" => $pattern]);
            return 0;
        } catch (Exception $e) {
            $this->logger->error("Failed to delete by pattern", ["pattern" => $pattern, "error" => $e->getMessage()]);
            throw new RuntimeException("Failed to delete pattern '{$pattern}' from Redis", 0, $e);
        }
    }

    /**
     * Check if a key exists in Redis
     */
    public function existsRedis(string $key): bool
    {
        try {
            return $this->cache->getItem($key)->isHit();
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Failed to check Redis key existence", ["key" => $key, "error" => $e->getMessage()]);
            throw new RuntimeException("Failed to check existence of key '{$key}' in Redis", 0, $e);
        }
    }

    /**
     * Clear all items from Redis
     */
    public function clearRedis(): void
    {
        try {
            $this->cache->clear();
            $this->logger->info("All Redis cache cleared");
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Failed to clear Redis cache", ["error" => $e->getMessage()]);
            throw new RuntimeException("Failed to clear Redis cache", 0, $e);
        }
    }
}
