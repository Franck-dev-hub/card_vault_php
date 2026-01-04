<?php
namespace App\Controller\Preferences;

use App\Service\LanguageManager;
use App\Service\UserPreferences\UserPreferencesService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/language")]
class LanguageController extends AbstractController
{
    public function __construct(
        private readonly LanguageManager $languageManager,
        private readonly UserPreferencesService $userPreferencesService
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    #[Route("/app", name: "change_app_language", methods: ["POST"])]
    public function changeAppLanguage(Request $request): Response
    {
        $language = $request->request->get("choice");
        if ($language) {
            $this->languageManager->setAppLanguage($language);

            if ($this->getUser()) {
                $this->userPreferencesService->language()->setAppLanguage(
                    $this->getUser()->getId(),
                    $language
                );
            }
        }

        return $this->redirect($request->headers->get("referer", '/'));
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route("/cards", name: "change_cards_language", methods: ["POST"])]
    public function changeCardsLanguage(Request $request): Response
    {
        $language = $request->request->get("choice");
        if ($language) {
            $this->languageManager->setCardsLanguage($language);

            if ($this->getUser()) {
                $this->userPreferencesService->language()->setCardLanguage(
                    $this->getUser()->getId(),
                    $language
                );
            }
        }

        return $this->redirect($request->headers->get("referer", '/'));
    }
}
