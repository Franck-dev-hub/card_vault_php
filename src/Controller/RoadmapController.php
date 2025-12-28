<?php

namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\LicenseServiceFactory;
use App\Service\PokemonService;
use App\Service\UserPreferencesService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoadmapController extends BaseController
{
    private const string PAGE_NAME = "roadmap";

    public function __construct(
        protected readonly LicenseServiceFactory $licenseFactory,
        UserPreferencesService                   $userPreferencesService,
        MenuService                              $footerService,
        TranslatorInterface                      $translator,
        LanguageManager                          $appLanguage,
        PokemonService                           $pokemonService,
    )
    {
        parent::__construct(
            $footerService,
            $translator,
            $userPreferencesService,
            $appLanguage,
            $pokemonService
        );
    }

    /**
     * @throws Exception
     */

    #[Route("/" . self::PAGE_NAME, name: self::PAGE_NAME)]
    public function roadmap(Request $request): Response
    {
        $files = [
            "roadmap-alpha.json",
            "roadmap-beta.json",
            "roadmap-rc.json",
            "roadmap-oos.json"
        ];

        $releases = [];
        $dataDir = $this->getParameter("kernel.project_dir") . "/data/roadmap/";

        // Implemented section
        $filePath = $dataDir . "roadmap-implemented.json";

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("File roadmap-implemented.json doesn't exist");
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if ($data === null) {
            throw new Exception("Error when decoding roadmap-implemented.json");
        }

        $implemented = $data;

        // Release section
        foreach ($files as $file) {
            $filePath = $dataDir . $file;

            if (!file_exists($filePath)) {
                throw $this->createNotFoundException("File $file doesn't exist");
            }

            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if ($data === null) {
                throw new Exception("Error when decoding $file");
            }

            $releases = array_merge($releases, $data);
        }

        return $this->renderPage(self::PAGE_NAME . ".html.twig", [
            "dir" => "routes",
            "implemented" => $implemented,
            "releases" => $releases,
            "currentPage" => self::PAGE_NAME
        ]);
    }
}
