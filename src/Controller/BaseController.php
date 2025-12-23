<?php
namespace App\Controller;

use App\Service\FooterService;
use App\Service\PokemonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly FooterService $footerService,
        protected readonly TranslatorInterface $translator,
        protected readonly string $appLanguage,
        protected readonly PokemonService $pokemonService
    ) {}

    protected function renderPage(string $template, array $data = []): Response
    {
        $this->translator->setLocale($this->appLanguage);

        $translationKey = $data["currentPage"] . ".title";
        $data["pageTitle"] = $this->translator->trans($translationKey);

        $data["buttons"] = $this->footerService->getButtons();
        $data["translator"] = $this->translator;

        return $this->render($template, $data);
    }
    #[Route("/{name}", name: "root")]
    public function root(string $name, Request $request): Response
    {
        $this->translator->setLocale($this->appLanguage);

        $data = [
            "name" => $name,
            "pageTitle" => $this->translator->trans($name . ".title"),
            "buttons" => $this->footerService->getButtons(),
            "currentPage" => $name,
            "translator" => $this->translator
        ];

        return $this->render("routes/{$name}.html.twig", $data);
    }
}
