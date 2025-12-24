<?php
namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

readonly class MenuService
{
    public function __construct(private TranslatorInterface $translator) {}
    public function getButtons(): array
    {
        $iconDir = "assets/icons/menu/";

        $buttons = [
            ["dashboard", "icon1.svg"],
            ["stats", "icon2.svg"],
            ["scan", "icon3.svg"],
            ["vault", "icon4.svg"],
            ["search", "icon5.svg"],
        ];

        $result = [];
        foreach ($buttons as $button) {
            $result[] = [
                "route" => $button[0],
                "label" => $this->translator->trans($button[0] . ".title"),
                "icon" => $iconDir . $button[1]
            ];
        }
        return $result;
    }
}
