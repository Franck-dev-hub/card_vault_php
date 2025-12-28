<?php
namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class UserPreferencesService
{
    public function __construct(
        private CacheItemPoolInterface $cache
    ) {}

    private function getKey(int $userId): string
    {
        return "user_preferences_" . $userId;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getPreferences(int $userId): array
    {
        $item = $this->cache->getItem($this->getKey($userId));

        if ($item->isHit()) {
            return $item->get();
        }

        return [
            "app_language" => "en",
            "card_language" => "en",
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setAppLanguage(int $userId, string $language): void
    {
        $key = $this->getKey($userId);
        $prefs = $this->getPreferences($userId);
        $prefs["app_language"] = $language;

        $item = $this->cache->getItem($key);
        $item->set($prefs);
        $item->expiresAfter(86400 * 365);
        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setCardLanguage(int $userId, string $language): void
    {
        $key = $this->getKey($userId);
        $prefs = $this->getPreferences($userId);
        $prefs["card_language"] = $language;

        $item = $this->cache->getItem($key);
        $item->set($prefs);
        $item->expiresAfter(86400 * 365);
        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAppLanguage(int $userId): string
    {
        $prefs = $this->getPreferences($userId);
        return $prefs["app_language"] ?? "en";
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getCardLanguage(int $userId): string
    {
        $prefs = $this->getPreferences($userId);
        return $prefs["card_language"] ?? "en";
    }
}
