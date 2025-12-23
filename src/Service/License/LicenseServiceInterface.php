<?php
namespace App\Service\License;

interface LicenseServiceInterface
{
    /**
     * Fetch all sets/extensions
     * Free format depending on API
     */
    public function getSets(): array;
}
