<?php
namespace App\Service;

use Exception;
use TCGdex\TCGdex;
use Symfony\Component\HttpClient\Psr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;

readonly class PokemonService
{
    public function __construct(
        private LanguageManager $languageManager
    ) {}

    private function getTcgdex(): TCGdex
    {
        $psr17Factory = new Psr17Factory();
        TCGdex::$requestFactory = $psr17Factory;
        TCGdex::$responseFactory = $psr17Factory;
        TCGdex::$client = new Psr18Client();
        TCGdex::$ttl = 3600 * 1000;

        return new TCGdex($this->languageManager->getCardsLanguage());
    }

    public function getCardsLanguage(): string
    {
        return $this->languageManager->getCardsLanguage();
    }

    /**
     * Fetch all Pokemon sets
     *
     * @return array<string, string> [setId => setName]
     * @throws Exception
     */
    public function getPokemonSets(): array
    {
        $tcgdex = $this->getTcgdex();
        $pokemonSets = $tcgdex->set->list();
        $extensions = [];

        foreach ($pokemonSets as $set) {
            $extensions[$set->id] = $set->name ?? $set->id;
        }

        return $extensions;
    }

    /**
     * Get set with its cards
     *
     * @return array{set: ?object, cards: array}
     * @throws Exception
     */
    public function getSetWithCards(string $setSelected): array
    {
        $tcgdex = $this->getTcgdex();
        $set = $tcgdex->set->get($setSelected);

        if ($set === null) {
            return ["set" => null, "cards" => []];
        }

        return [
            "set" => $set,
            "cards" => $set->cards ?? []
        ];
    }

    /**
     * Get serie and extract set cards
     *
     * @return array{set: ?object, cards: array}
     * @throws Exception
     */
    public function getSerieCards(string $setSelected): array
    {
        $tcgdex = $this->getTcgdex();
        $serie = $tcgdex->serie->get($setSelected);

        if (empty($serie->sets)) {
            return ["set" => null, "cards" => []];
        }

        $setResume = $serie->sets[0];
        $currentSet = $setResume->toSet();

        return [
            "set" => $currentSet,
            "cards" => $currentSet->cards ?? []
        ];
    }
}
