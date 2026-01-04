<?php
namespace App\Service\UserPreferences;

use Psr\Cache\InvalidArgumentException;

readonly class MarketplacePreferences
{
    private const array AVAILABLE_MARKETPLACES = [
        "cardmarket" => "CardMarket",
        "tcgplayer" => "TCGplayer"
    ];

    public function __construct(
        private UserPreferencesService $service
    ) {}

    public function getAvailableMarketplaces(): array
    {
        return $this::AVAILABLE_MARKETPLACES;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setAppMarketplace(int $userId, string $marketplace): void
    {
        $this->service->setPreference($userId, "app_marketplace", $marketplace);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAppMarketplace(int $userId): string
    {
        return $this->service->getPreference($userId, "app_marketplace", "euro");
    }
}
