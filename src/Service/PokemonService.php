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

    private function getTcgdex(?string $language = null): TCGdex
    {
        $psr17Factory = new Psr17Factory();
        TCGdex::$requestFactory = $psr17Factory;
        TCGdex::$responseFactory = $psr17Factory;
        TCGdex::$client = new Psr18Client();
        TCGdex::$ttl = 3600 * 1000;

        $lang = $language ?? $this->languageManager->getCardsLanguage();
        return new TCGdex($lang);
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

        $cards = $set->cards ?? [];
        $this->enrichCardsWithImageFallback($cards);

        return [
            "set" => $set,
            "cards" => $cards
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
        $cards = $currentSet->cards ?? [];
        $this->enrichCardsWithImageFallback($cards);

        return [
            "set" => $currentSet,
            "cards" => $cards
        ];
    }

    /**
     * Get image with fallback to English if not available in current language
     *
     * @param string $cardId
     * @return string|null
     * @throws Exception
     */
    private function getCardImageWithFallback(string $cardId): ?string
    {
        $currentLanguage = null;
        $currentLang = $currentLanguage ?? $this->languageManager->getCardsLanguage();

        // Try to get card in current language
        $tcgdex = $this->getTcgdex($currentLang);
        $card = $tcgdex->card->get($cardId);

        if ($card !== null && !empty($card->image)) {
            return $card->image;
        }

        // Fallback to English if image not found and current language is not English
        if ($currentLang !== 'en') {
            $tcgdexEn = $this->getTcgdex('en');
            $cardEn = $tcgdexEn->card->get($cardId);

            if ($cardEn !== null && !empty($cardEn->image)) {
                return $cardEn->image;
            }
        }

        return null;
    }

    /**
     * Enrich cards array with image fallback
     *
     * @param array $cards
     * @return void
     * @throws Exception
     */
    private function enrichCardsWithImageFallback(array $cards): void
    {
        foreach ($cards as $card) {
            if (empty($card->image) && isset($card->id)) {
                $card->image = $this->getCardImageWithFallback($card->id);
            }
        }
    }

    /**
     * Get a single card by ID
     *
     * @param string $cardId
     * @return array|null
     * @throws Exception
     */
    public function getCardById(string $cardId): ?array
    {
        try {
            $tcgdex = $this->getTcgdex();
            $card = $tcgdex->card->get($cardId);

            if ($card === null) {
                return null;
            }

            // Handle variants
            $variants = null;
            if (isset($card->variants)) {
                $variantObj = get_object_vars($card->variants);
                $availableVariants = array_filter($variantObj, fn($value) => $value === true);

                if (!empty($availableVariants)) {
                    $variants = array_keys($availableVariants);
                }
            }

            // Get image with fallback
            $image = $this->getCardImageWithFallback($cardId);

            // Convert in JSON
            return [
                "id" => $card->id ?? $cardId,
                "illustrator" => $card->illustrator ?? null,
                "image" => $image,
                "localId" => $card->localId ?? null,
                "name" => $card->name ?? null,
                "rarity" => $card->rarity ?? null,
                "setOfficial" => $card->set->cardCount->official ?? null,
                "setTotal" => $card->set->cardCount->total ?? null,
                "setId" => $card->set->id ?? null,
                "setLogo" => $card->set->logo ?? null,
                "setName" => $card->set->name ?? null,
                "setSymbol" => $card->set->symbol ?? null,
                "variants" => $variants,
                "dexId" => $card->dexId[0] ?? null,
                "type" => $card->types[0] ?? null,
                "evolveFrom" => $card->evolveFrom ?? null,
                "description" => $card->description ?? null,
                "stage" => $card->stage ?? null,
                "regulationMark" => $card->regulationMark ?? null,
                "price" => $card->pricing->cardmarket->avg ?? null,
            ];
        } catch (Exception $e) {
            throw new Exception("Error fetching card $cardId: " . $e->getMessage());
        }
    }
}
