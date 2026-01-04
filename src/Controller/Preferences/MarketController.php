<?php

namespace App\Controller\Preferences;

use App\Service\UserPreferences\UserPreferencesService;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/market")]
class MarketController extends AbstractController
{
    public function __construct(
        private readonly UserPreferencesService $userPreferencesService,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    #[Route("/currency", name: "market_change_currency", methods: ["POST"])]
    public function changeCurrency(Request $request): Response
    {
        $currency = $request->request->get("choice");
        $user = $this->getUser();

        $this->logger->info("changeCurrency called", [
            "currency" => $currency,
            "userId" => $user?->getId(),
            "hasUser" => $user !== null,
        ]);

        if ($currency && $user) {
            $this->logger->info("Saving currency preference", [
                "userId" => $user->getId(),
                "currency" => $currency,
            ]);

            $this->userPreferencesService->currency()->setAppCurrency(
                $user->getId(),
                $currency
            );

            $this->logger->info("Currency preference saved successfully");
        } else {
            $this->logger->warning("Cannot save: missing currency or user", [
                "currency" => $currency,
                "user" => $user?->getId(),
            ]);
        }

        return $this->redirect($request->headers->get("referer", '/'));
    }


    /**
     * @throws InvalidArgumentException
     */
    #[Route("/marketplace", name: "market_change_marketplace", methods: ["POST"])]
    public function changeMarketplace(Request $request): Response
    {
        $marketplace = $request->request->get("choice");
        if ($marketplace && $this->getUser()) {
            $this->userPreferencesService->marketplace()->setAppMarketplace(
                $this->getUser()->getId(),
                $marketplace
            );
        }

        return $this->redirect($request->headers->get("referer", '/'));
    }
}
