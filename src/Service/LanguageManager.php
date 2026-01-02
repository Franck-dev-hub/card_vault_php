<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class LanguageManager
{
    private const array AVAILABLE_LANGUAGES = [
        "en" => "English",
        "fr" => "Français",
        "it" => "Italiano",
        "de" => "Deutsch",
        "es" => "Español",
        "pt" => "Português",
    ];

    private const string SESSION_APP_LANGUAGE = "app_language";
    private const string SESSION_CARDS_LANGUAGE = "cards_language";
    private const string DEFAULT_APP_LANGUAGE = "en";
    private const string DEFAULT_CARDS_LANGUAGE = "en";

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function getAppLanguage(): string
    {
        return $this->getSessionValue(self::SESSION_APP_LANGUAGE, self::DEFAULT_APP_LANGUAGE);
    }

    public function setAppLanguage(string $language): void
    {
        if ($this->isValidLanguage($language)) {
            $this->setSessionValue(self::SESSION_APP_LANGUAGE, $language);
        }
    }

    public function getCardsLanguage(): string
    {
        return $this->getSessionValue(self::SESSION_CARDS_LANGUAGE, self::DEFAULT_CARDS_LANGUAGE);
    }

    public function setCardsLanguage(string $language): void
    {
        if ($this->isValidLanguage($language)) {
            $this->setSessionValue(self::SESSION_CARDS_LANGUAGE, $language);
        }
    }

    /**
     * Check if language code is valid
     */
    public function isValidLanguage(string $language): bool
    {
        return isset(self::AVAILABLE_LANGUAGES[$language]);
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

    public function getAvailableLanguages(): array
    {
        return self::AVAILABLE_LANGUAGES;
    }
}
