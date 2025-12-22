<?php
namespace App\Controller;

use App\Service\FooterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly FooterService $footerService,
        protected readonly TranslatorInterface $translator,
        protected readonly string $appLanguage
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
}
