<?php
namespace App\Service\UserPreferences;

use Psr\Cache\InvalidArgumentException;

readonly class CurrencyPreferences
{
    public function __construct(
        private UserPreferencesService $service
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function setAppCurrency(int $userId, string $currency): void
    {
        $this->service->setPreference($userId, "app_currency", $currency);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAppCurrency(int $userId): string
    {
        return $this->service->getPreference($userId, "app_currency", "euro");
    }
}
