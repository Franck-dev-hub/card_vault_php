<?php
namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends BaseController
{
    public function __construct(
        protected readonly LicenseServiceFactory $licenseFactory,
        MenuService                              $footerService,
        TranslatorInterface                      $translator,
        LanguageManager                          $appLanguage,
        PokemonService                           $pokemonService,
    ) {
        parent::__construct($footerService, $translator, $appLanguage, $pokemonService);
    }

    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        $licenseSelected = $request->request->get("license", '');
        $setSelected = $request->request->get("choice", '');

        $extensions = [];
        $cards = [];
        $currentSet = null;

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
            $extensions = $licenseService->fetchPokemonSets();
        } catch (Exception $e) {
            $this->addFlash("error", $e->getMessage());
        }

        // Handle set selection based on license
        if ($setSelected) {
            try {
                [$currentSet, $cards] = match($licenseSelected) {
                    "pokemon" => $licenseService->handlePokemonSetSelection($setSelected),
                    // "yugioh" => $licenseService->handleSetSelection($setSelected),
                    // "magic" => $licenseService->handleSetSelection($setSelected),
                    default => [null, []],
                };
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
