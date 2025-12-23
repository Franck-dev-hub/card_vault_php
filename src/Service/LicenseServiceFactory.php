<?php
namespace App\Service;

use App\Service\License\LicenseServiceInterface;

class LicenseServiceFactory
{
    public function __construct(
        private LicenseServiceInterface $pokemonService,
        // private YuGiOhLicenseService $yugiohService,
        // private MagicLicenseService $magicService,
    ) {}

    public function getLicenseService(string $license): ?LicenseServiceInterface
    {
        return match($license) {
            "pokemon" => $this->pokemonService,
            // "yugioh" => $this->yugiohService,
            // "magic" => $this->magicService,
            default => null,
        };
    }
}
