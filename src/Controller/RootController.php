<?php
namespace App\Controller;

use App\Service\FooterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RootController extends AbstractController
{
    public function __construct(
        private readonly FooterService $footerService,
        private readonly TranslatorInterface $translator,
        private readonly string $appLanguage
    ) {}

    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->redirectToRoute("root", ["name" => "dashboard"]);
    }

    #[Route("/{name}", name: "root")]
    public function root(string $name): Response
    {
        $this->translator->setLocale($this->appLanguage);
        return $this->render("routes/{$name}.html.twig", [
            "name" => $name,
            "pageTitle" => $this->translator->trans($name . ".title"),
            "buttons" => $this->footerService->getButtons(),
            "currentPage" => $name,
            "translator" => $this->translator
        ]);
    }
}
