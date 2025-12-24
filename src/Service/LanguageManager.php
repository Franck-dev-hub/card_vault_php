<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class LanguageManager
{
    private const SESSION_APP_LANGUAGE = "app_language";
    private const SESSION_CARDS_LANGUAGE = "cards_language";
    private const DEFAULT_APP_LANGUAGE = "en";
    private const DEFAULT_CARDS_LANGUAGE = "en";

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function getAppLanguage(): string
    {
        return $this->getSessionValue(self::SESSION_APP_LANGUAGE, self::DEFAULT_APP_LANGUAGE);
    }

    public function setAppLanguage(string $language): void
    {
        $this->setSessionValue(self::SESSION_APP_LANGUAGE, $language);
    }

    public function getCardsLanguage(): string
    {
        return $this->getSessionValue(self::SESSION_CARDS_LANGUAGE, self::DEFAULT_CARDS_LANGUAGE);
    }

    public function setCardsLanguage(string $language): void
    {
        $this->setSessionValue(self::SESSION_CARDS_LANGUAGE, $language);
    }

    private function getSessionValue(string $key, string $default): string
    {
            $session = $this->requestStack->getSession();
            return $session->get($key, $default);
    }

    private function setSessionValue(string $key, string $value): void
    {
        $session = $this->requestStack->getSession();
        $session->set($key, $value);
    }
}
