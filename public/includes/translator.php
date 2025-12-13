<?php

class Translator {
    private static array $translations = [];
    private static $currentLang = "fr_FR";

    public static function init($lang = 'fr_FR'): void {
        self::$currentLang = $lang;
        $langFile = __DIR__ . "/../lang/{$lang}.json";

        if (file_exists($langFile)) {
            $json = file_get_contents($langFile);
            self::$translations = json_decode($json, true);
        }
    }

    public static function get($key) {
        $keys = explode('.', $key);
        $value = self::$translations;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key;
            }
        }
        return $value;
    }
}

function t($key) {
    return Translator::get($key);
}
?>