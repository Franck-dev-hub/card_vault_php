<?php
namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\PokemonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly MenuService         $footerService,
        protected readonly TranslatorInterface $translator,
        protected readonly LanguageManager     $languageManager,
        protected readonly PokemonService      $pokemonService
    ) {}

    protected function renderPage(string $template, array $data = []): Response
    {
        $appLanguage = $this->languageManager->getAppLanguage();
        $this->translator->setLocale($appLanguage);

        $translationKey = $data["currentPage"] . ".title";
        $data["pageTitle"] = $this->translator->trans($translationKey);
        $data["buttons"] = $this->footerService->getButtons();
        $data["translator"] = $this->translator;
        $data["languageManager"] = $this->languageManager;
        $data["availableLanguages"] = $this->languageManager->getAvailableLanguages();

        return $this->render($template, $data);
    }

    #[Route("/{name}", name: "root")]
    public function root(string $name): Response
    {
        $appLanguage = $this->languageManager->getAppLanguage();
        $this->translator->setLocale($appLanguage);

        $data = [
            "name" => $name,
            "pageTitle" => $this->translator->trans($name . ".title"),
            "buttons" => $this->footerService->getButtons(),
            "currentPage" => $name,
            "translator" => $this->translator,
            "languageManager" => $this->languageManager,
            "availableLanguages" => $this->languageManager->getAvailableLanguages()
        ];

        return $this->render("routes/$name.html.twig", $data);
    }
}
