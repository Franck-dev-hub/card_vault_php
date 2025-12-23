<?php
namespace App\Service\License;

use App\Service\PokemonService;
use Exception;

readonly class PokemonLicenseService implements LicenseServiceInterface
{
    public function __construct(private PokemonService $pokemonService) {}

    /**
     * @throws Exception
     */
    public function getSets(): array
    {
        return $this->pokemonService->getPokemonSets();
    }

    /**
     * @throws Exception
     */
    public function getSetWithCards(string $setId): array
    {
        return $this->pokemonService->getSetWithCards($setId);
    }

    /**
     * @throws Exception
     */
    public function getSerieCards(string $setId): array
    {
        return $this->pokemonService->getSerieCards($setId);
    }

    /**
     * Fetch Pokemon sets with error handling
     */
    public function fetchSets(): array
    {
        try {
            $sets = $this->getSets();
            return array_reverse($sets, preserve_keys: true);
        } catch (Exception $e) {
            throw new Exception("Can't find Pokémon sets");
        }
    }

    /**
     * Handle Pokemon set selection with fallback logic
     *
     * @return array{0: ?object, 1: array}
     * @throws Exception
     */
    public function handleSetSelection(string $setId): array
    {
        try {
            $result = $this->getSetWithCards($setId);
            if ($result["set"] !== null) {
                return [$result["set"], $result["cards"]];
            }
        } catch (Exception $e) {
            throw new Exception("Pokemon error");
        }

        try {
            $result = $this->getSerieCards($setId);
            return [$result["set"], $result["cards"]];
        } catch (Exception $e) {
            throw new Exception("Pokemon fallback error");
        }
    }
}
