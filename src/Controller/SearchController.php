<?php

namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use App\Service\RedisService;
use App\Service\UserPreferencesService;
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
        protected readonly UserPreferencesService $userPreferencesService,
        protected readonly LanguageManager        $languageManager,
        protected readonly MenuService            $footerService,
        protected readonly PokemonService         $pokemonService,
        protected readonly TranslatorInterface    $translator,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();

        $licenseSelected = $request->request->get("license", '');
        $setSelected = $request->request->get("choice", '');

        $extensions = [];
        $cards = [];
        $currentSet = null;

        // Get user preferences
        if (!$licenseSelected && $userId) {
            $prefs = $this->userPreferencesService->getPreferences($userId);
            $licenseSelected = $prefs["search_license"] ?? '';
        }

        if (!$setSelected && $userId) {
            $prefs = $this->userPreferencesService->getPreferences($userId);
            $setSelected = $prefs["search_set"] ?? '';
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

                // Save user preferences
                if ($userId) {
                    $this->userPreferencesService->setSearchPreferences(
                        $userId,
                        $licenseSelected,
                        $setSelected
                    );
                }
            } catch (Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        // Reset button management
        if ($request->request->has("reset")) {
            if ($userId) {
                $this->userPreferencesService->resetSearchPreferences($userId);
            }
            return $this->redirectToRoute("search");
        }

        return $this->renderPage("search.html.twig", [
            "dir" => "routes",
            "licenseSelected" => $licenseSelected,
            "setSelected" => $setSelected,
            "extensions" => $extensions,
            "cards" => $cards,
            "currentSet" => $currentSet,
            "currentPage" => "search"
        ]);
    }
}
