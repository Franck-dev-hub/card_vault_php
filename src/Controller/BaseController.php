<?php
namespace App\Controller;

use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\PokemonService;
use App\Service\UserPreferences\UserPreferencesService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly MenuService            $footerService,
        protected readonly TranslatorInterface    $translator,
        protected readonly UserPreferencesService $userPreferencesService,
        protected readonly LanguageManager        $languageManager,
        protected readonly PokemonService         $pokemonService
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    protected function renderPage(string $template, array $data = []): Response
    {
        if ($this->getUser()) {
            $userId = $this->getUser()->getId();

            $appLanguage = $this->userPreferencesService->language()->getAppLanguage($userId);
            $cardLanguage = $this->userPreferencesService->language()->getCardLanguage($userId);
            $appCurrency = $this->userPreferencesService->currency()->getAppCurrency($userId);
            $appMarketplace = $this->userPreferencesService->marketplace()->getAppMarketplace($userId);

            $this->languageManager->setAppLanguage($appLanguage);
            $this->languageManager->setCardsLanguage($cardLanguage);

            $data["currentCurrency"] = $appCurrency;
            $data["currentMarketplace"] = $appMarketplace;
        } else {
            $appLanguage = $this->languageManager->getAppLanguage();
        }

        $this->translator->setLocale($appLanguage);

        // Set page title from translation key if currentPage is provided
        if (isset($data["currentPage"])) {
            $translationKey = $data["currentPage"] . ".title";
            $data["pageTitle"] = $this->translator->trans($translationKey);
        }

        // Set common template variables
        $data["buttons"] = $this->footerService->getButtons();
        $data["translator"] = $this->translator;
        $data["languageManager"] = $this->languageManager;
        $data["availableAppLanguages"] = $this->languageManager->getAvailableAppLanguages();
        $data["availableLanguages"] = $this->languageManager->getAvailableLanguages();
        $data["availableCurrencies"] = $this->userPreferencesService->currency()->getAvailableCurrencies();
        $data["availableMarketplaces"] = $this->userPreferencesService->marketplace()->getAvailableMarketplaces();

        // Build full template path if dir is provided and template doesn't already contain path
        if (isset($data["dir"]) && $data["dir"] && !str_contains($template, '/')) {
            $template = $data["dir"] . '/' . $template;
        }

        return $this->render($template, $data);
    }
}
