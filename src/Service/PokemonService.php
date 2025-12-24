<?php
namespace App\Service;

use Exception;
use TCGdex\TCGdex;
use Symfony\Component\HttpClient\Psr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;

class PokemonService
{
    private TCGdex $tcgdex;
    private bool $initialized = false;

    public function __construct(private readonly LanguageManager $languageManager)
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $psr17Factory = new Psr17Factory();
        TCGdex::$requestFactory = $psr17Factory;
        TCGdex::$responseFactory = $psr17Factory;
        TCGdex::$client = new Psr18Client();
        TCGdex::$ttl = 3600 * 1000;
        $this->tcgdex = new TCGdex($this->languageManager->getCardsLanguage());
        $this->initialized = true;
    }

    /**
     * Fetch all Pokemon sets
     *
     * @return array<string, string> [setId => setName]
     * @throws Exception
     */
    public function getPokemonSets(): array
    {
        $extensions = [];
        $pokemonSets = $this->tcgdex->set->list();

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
        $set = $this->tcgdex->set->get($setSelected);

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
        $serie = $this->tcgdex->serie->get($setSelected);

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
