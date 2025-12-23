<?php
namespace App\Controller;

use App\Service\FooterService;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends BaseController
{
    public function __construct(
        protected readonly LicenseServiceFactory $licenseFactory,
        FooterService $footerService,
        TranslatorInterface $translator,
        string $appLanguage,
        PokemonService $pokemonService,
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
            $this->addFlash("error", "search.error-license");
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
            $extensions = $licenseService->fetchSets();
        } catch (\Exception $e) {
            $this->addFlash("error", $e->getMessage());
        }

        // Handle set selection based on license
        if ($setSelected) {
            try {
                [$currentSet, $cards] = match($licenseSelected) {
                    "pokemon" => $licenseService->handleSetSelection($setSelected),
                    // "yugioh" => $licenseService->handleSetSelection($setSelected),
                    // "magic" => $licenseService->handleSetSelection($setSelected),
                    default => [null, []],
                };
            } catch (\Exception $e) {
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
