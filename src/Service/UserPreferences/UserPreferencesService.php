<?php
namespace App\Service\UserPreferences;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class UserPreferencesService
{
    private const int CACHE_TTL = 86400 * 365;
    private const array DEFAULTS = [
        "app_language" => "en",
        "card_language" => "en",
        "app_currency" => "euro",
        "price_platform" => "cardmarket"
    ];

    public function __construct(
        private CacheItemPoolInterface $cache,
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

        return self::DEFAULTS;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function savePreferences(int $userId, array $prefs): void
    {
        $item = $this->cache->getItem($this->getKey($userId));
        $item->set($prefs);
        $item->expiresAfter(self::CACHE_TTL);
        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setPreference(int $userId, string $key, mixed $value): void
    {
        $prefs = $this->getPreferences($userId);
        $prefs[$key] = $value;

        $this->cache->deleteItem($this->getKey($userId));

        $this->savePreferences($userId, $prefs);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getPreference(int $userId, string $key, mixed $default = null): mixed
    {
        $prefs = $this->getPreferences($userId);
        return $prefs[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public function language(): LanguagePreferences
    {
        return new LanguagePreferences($this);
    }

    public function currency(): CurrencyPreferences
    {
        return new CurrencyPreferences($this);
    }

    public function marketplace(): MarketplacePreferences
    {
        return new MarketplacePreferences($this);
    }

    public function search(): SearchPreferences
    {
        return new SearchPreferences($this);
    }
}
