<?php
namespace App\Service\UserPreferences;

use Psr\Cache\InvalidArgumentException;

readonly class SearchPreferences
{
    public function __construct(
        private UserPreferencesService $service
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function setSearchPreferences(int $userId, string $license, string $set): void
    {
        $prefs = $this->service->getPreferences($userId);
        $prefs["search_license"] = $license;
        $prefs["search_set"] = $set;
        $this->service->savePreferences($userId, $prefs);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function resetSearchPreferences(int $userId): void
    {
        $prefs = $this->service->getPreferences($userId);
        unset($prefs["search_license"], $prefs["search_set"]);
        $this->service->savePreferences($userId, $prefs);
    }
}
