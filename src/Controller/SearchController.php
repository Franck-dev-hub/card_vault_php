<?php

namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use App\Service\RedisService;
use App\Service\License\TcgdexRedisService;

use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends BaseController
{
    public function __construct(
        protected readonly CacheItemPoolInterface $cache,
        protected readonly LicenseServiceFactory  $licenseFactory,
        protected readonly RedisService           $redisService,
        protected readonly TcgdexRedisService     $tcgdexRedisService,
        LanguageManager                           $appLanguage,
        MenuService                               $footerService,
        PokemonService                            $pokemonService,
        TranslatorInterface                       $translator,
    )
    {
        parent::__construct($footerService, $translator, $appLanguage, $pokemonService);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        $licenseSelected = $request->request->get("license", '');
        $setSelected = $request->request->get("choice", '');

        $extensions = [];
        $cards = [];
        $currentSet = null;

        if (!$licenseSelected) {
            $licenseSelected = $this->redisService->getRedis($this->cache, "search_license") ?? '';
        }

        if (!$setSelected) {
            $setSelected = $this->redisService->getRedis($this->cache, "search_set") ?? '';
        }

        $licenseService = $this->licenseFactory->getLicenseService($licenseSelected);

        if (!$licenseService) {
            $this->addFlash("error", "License not supported");
            return $this->renderPage("routes/search.html.twig", [
                "licenseSelected" => $licenseSelected,
                "setSelected" => $setSelected,
                "extensions" => $extensions,
                "cards" => $cards,
                "currentSet" => $currentSet,
                "currentPage" => "search"
            ]);
        }

        // Fetch sets
        try {
            $extensions = $this->tcgdexRedisService->getExtensions($licenseSelected, $licenseService);
        } catch (Exception $e) {
            $this->addFlash("error", $e->getMessage());
        }

        // Handle set selection based on license
        if ($setSelected) {
            try {
                [$currentSet, $cards] = match ($licenseSelected) {
                    "pokemon" => $this->tcgdexRedisService->getCards(
                        $licenseSelected,
                        $setSelected,
                        $licenseService
                    ),
                    // "yugioh" => $licenseService->handleSetSelection($setSelected),
                    // "magic" => $licenseService->handleSetSelection($setSelected),
                    default => [null, []],
                };
                $this->redisService->storeRedis($this->cache, "search_license", $licenseSelected, 84600);
                $this->redisService->storeRedis($this->cache, "search_set", $setSelected, 84600);
            } catch (Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->renderPage("routes/search.html.twig", [
            "licenseSelected" => $licenseSelected,
            "setSelected" => $setSelected,
            "extensions" => $extensions,
            "cards" => $cards,
            "currentSet" => $currentSet,
            "currentPage" => "search"
        ]);
    }
}
