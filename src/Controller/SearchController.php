<?php

namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use App\Service\RedisService;
use App\Service\UserPreferences\UserPreferencesService;
use App\Service\License\TcgdexRedisService;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * Get card details by ID for modal
     */
    #[Route("/card/{id}/details", name: "card_details", methods: ["GET"])]
    public function cardDetails(string $id, Request $request): Response
    {
        try {
            $license = $request->query->get("license", "pokemon");

            // Get the license service
            $licenseService = $this->licenseFactory->getLicenseService($license);
            if (!$licenseService) {
                return $this->json(
                    ["error" => "License not supported"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Fetch card details
            $card = $this->tcgdexRedisService->getCardById($license, $id, $licenseService);

            if (!$card) {
                return $this->json(
                    ["error" => "Card not found"],
                    Response::HTTP_NOT_FOUND
                );
            }

            // Format the image URL
            $image = $card["image"] ?? null;
            if ($image && !str_ends_with($image, ".webp")) {
                $image = $image . "/high.webp";
            }

            // Format set logo
            $setLogo = $card["setLogo"] ?? null;
            if ($setLogo && !str_ends_with($setLogo, ".webp")) {
                $setLogo = $setLogo . ".webp";
            }

            // Format set symbol
            $setSymbol = $card["setSymbol"] ?? null;
            if ($setSymbol) {
                $setSymbol = $setSymbol . ".webp";
            }

            return $this->render("/routes/search/cardModal.html.twig", [
                "card" => $card,
                "image" => $image,
                "setLogo" => $setLogo,
                "setSymbol" => $setSymbol,
            ]);
        } catch (Exception $e) {
            return $this->json(
                ["error" => "Error loading card: " . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
