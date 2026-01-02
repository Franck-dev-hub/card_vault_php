<?php
namespace App\Service\UserPreferences;

use Psr\Cache\InvalidArgumentException;

readonly class LanguagePreferences
{
    public function __construct(
        private UserPreferencesService $service
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function setAppLanguage(int $userId, string $language): void
    {
        $this->service->setPreference($userId, "app_language", $language);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAppLanguage(int $userId): string
    {
        return $this->service->getPreference($userId, "app_language", "en");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setCardLanguage(int $userId, string $language): void
    {
        $this->service->setPreference($userId, "card_language", $language);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getCardLanguage(int $userId): string
    {
        return $this->service->getPreference($userId, "card_language", "en");
    }
}
